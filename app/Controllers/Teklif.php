<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\TeklifStore;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Embedded offer builder (/teklif) — CI4 port of apps/teklif.
 *
 * The SPA itself is served as plain statics from public/teklif/ui/ (never
 * enters CI4); this controller only covers the dynamic surface behind the
 * `appsauth` filter:
 *   - api():   1:1 port of apps/teklif/server/api/{save,list,open,delete}.php
 *   - data():  whitelisted JSON files from writable/data/teklif/data/
 *   - offer(): saved offer output files from writable/data/teklif/offers/
 *              (path-traversal guard ported from apps/teklif/gate.php)
 *
 * Deliberately NO cachePage() here, and the API routes are exempt from the
 * global CSRF filter (see app/Config/Filters.php) — the SPA carries no token;
 * the appsauth session + SameSite=Lax cookie protect the endpoints instead.
 */
class Teklif extends BaseController
{
    /** Only these files may be read through data(). */
    private const DATA_WHITELIST = ['products.json', 'templates.json', 'company.json', 'counter.json'];

    /** MIME map for offer output files (ported from apps/teklif/gate.php). */
    private const MIME = [
        'html'  => 'text/html; charset=utf-8',
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'text/javascript; charset=utf-8',
        'json'  => 'application/json; charset=utf-8',
        'pdf'   => 'application/pdf',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'webp'  => 'image/webp',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff2' => 'font/woff2',
    ];

    /** GET /teklif and /teklif/index.php — bounce to the static SPA shell. */
    public function index(): RedirectResponse
    {
        return redirect()->to(site_url('teklif/ui/index.html'));
    }

    /**
     * GET|POST /teklif/server/api/{name} — API dispatcher.
     *
     * $name arrives as "save.php" | "list.php" | "open.php" | "delete.php"
     * (the SPA calls the legacy .php URLs); anything else is a JSON 404.
     */
    public function api(string $name = ''): ResponseInterface
    {
        return match ($name) {
            'save.php'   => $this->apiSave(),
            'list.php'   => $this->apiList(),
            'open.php'   => $this->apiOpen(),
            'delete.php' => $this->apiDelete(),
            default      => $this->respond(['ok' => false, 'error' => 'not_found'], 404),
        };
    }

    /** GET /teklif/data/{file} — whitelisted read-only JSON lookups. */
    public function data(string $file = ''): ResponseInterface
    {
        if (! in_array($file, self::DATA_WHITELIST, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $dataDir = TeklifStore::dataDir();
        $path    = $dataDir . '/' . $file;
        $body    = null;

        // writable/ deploy paketinde izlenmez. İlk canlı kurulumda katalog
        // dosyalarını repoda izlenen apps/teklif/data kaynağından hazırla;
        // mevcut (ve çalışma sırasında değişmiş olabilecek) dosyaya dokunma.
        if (! is_file($path)) {
            if (! is_dir($dataDir)) {
                @mkdir($dataDir, 0775, true);
            }

            $seed = ROOTPATH . 'apps/teklif/data/' . $file;
            if (is_file($seed)) {
                @copy($seed, $path);
                // Paylaşımlı hostta writable izinleri yanlışsa katalog yine
                // okunabilsin; yazılabilir kopya sonraki doğru deployda oluşur.
                if (! is_file($path)) {
                    $path = $seed;
                }
            } elseif ($file === 'counter.json') {
                $initialCounter = ['year' => (int) date('Y'), 'seq' => 0];
                TeklifStore::jwrite($path, $initialCounter);
                if (! is_file($path)) {
                    $body = json_encode($initialCounter, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        if (! is_file($path) && $body === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody($body ?? (string) file_get_contents($path));
    }

    /**
     * GET /teklif/offers/{folder}/{file} — serve saved offer outputs.
     *
     * Security model ported from apps/teklif/gate.php: reject null bytes and
     * dot-leading segments, then require the resolved realpath to stay under
     * the offers root. Offer outputs are personal data — never cached.
     */
    public function offer(string ...$segments): ResponseInterface
    {
        $rel = str_replace('\\', '/', implode('/', $segments));

        if ($rel === '' || str_contains($rel, "\0") || preg_match('#(^|/)\.#', $rel)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $base = realpath(TeklifStore::offersDir());
        $full = $base !== false ? realpath($base . '/' . $rel) : false;

        if ($full === false || ! is_file($full)
            || ! str_starts_with($full, $base . DIRECTORY_SEPARATOR)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $mime = self::MIME[strtolower(pathinfo($full, PATHINFO_EXTENSION))] ?? null;
        if ($mime === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $response = $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) filesize($full))
            ->setHeader('Cache-Control', 'no-store')
            ->setBody((string) file_get_contents($full));

        if ($mime === 'application/pdf') {
            // Render in the browser tab; the SPA offers its own download link.
            $response->setHeader('Content-Disposition', 'inline; filename="' . basename($full) . '"');
        }

        return $response;
    }

    // ------------------------------------------------------------------
    // API endpoints — ported 1:1 from apps/teklif/server/api/*.php
    // ------------------------------------------------------------------

    /** save.php: offer folder + offer.json + offer.html + (best-effort) offer.pdf. */
    private function apiSave(): ResponseInterface
    {
        $in    = $this->jsonBody();
        $offer = $in['offer'] ?? null;
        $html  = $in['html'] ?? '';

        if (! $offer) {
            return $this->respond(['ok' => false, 'message' => 'Teklif verisi alınamadı']);
        }

        $offers = TeklifStore::offersDir();
        @mkdir($offers, 0777, true);

        $no   = TeklifStore::safeName($offer['offer_no'] ?? '') ?: 'TKL';
        $name = $offer['customer']['name'] ?? ($offer['customer']['company'] ?? '');
        $slug = TeklifStore::slugify($name);
        $iso  = TeklifStore::dateISO($offer['date'] ?? '');

        // Same offer_no folder exists -> UPDATE (don't bump the counter).
        $existing = null;

        foreach (glob($offers . '/*', GLOB_ONLYDIR) ?: [] as $d) {
            if (strpos(basename($d), '_' . $no) !== false) {
                $existing = $d;
                break;
            }
        }
        $isUpdate = $existing !== null;

        $dir = $isUpdate ? $existing : ($offers . '/' . $iso . '_' . $slug . '_' . $no);
        @mkdir($dir, 0777, true);

        TeklifStore::jwrite($dir . '/offer.json', $offer);
        file_put_contents($dir . '/offer.html', $html);

        // Counter (only bumped for brand-new offers).
        $counterFile = TeklifStore::dataDir() . '/counter.json';
        $counter     = TeklifStore::jread($counterFile, ['year' => (int) date('Y'), 'seq' => 0]);

        if (! $isUpdate) {
            $counter['seq']  = ($counter['seq'] ?? 0) + 1;
            $counter['year'] = (int) date('Y');
            TeklifStore::jwrite($counterFile, $counter);
        }

        // PDF via headless Chrome/Edge. When no browser exists the request
        // still succeeds: pdf_created=false makes the SPA fall back to the
        // print dialog. (The original also opened Windows Explorer here —
        // intentionally dropped in the web context.)
        $browser    = TeklifStore::findBrowser();
        $pdfCreated = $browser !== null && TeklifStore::makePdf($dir . '/offer.html', $dir . '/offer.pdf');

        $out = [
            'ok'          => true,
            'folder'      => basename($dir),
            'offer_no'    => $no,
            'seq'         => $counter['seq'] ?? null,
            'updated'     => $isUpdate,
            'pdf_created' => $pdfCreated,
            'path'        => $dir,
        ];

        if ($browser === null) {
            $out['pdf']       = false;
            $out['pdf_error'] = 'chrome_unavailable';
        }

        return $this->respond($out);
    }

    /** list.php: saved offers, newest first. */
    private function apiList(): ResponseInterface
    {
        $out = [];

        foreach (glob(TeklifStore::offersDir() . '/*', GLOB_ONLYDIR) ?: [] as $d) {
            $o = TeklifStore::jread($d . '/offer.json', []);

            // Product summary — supports new items[] and the old single product.
            $product = '';

            if (! empty($o['items']) && is_array($o['items'])) {
                $names = [];

                foreach ($o['items'] as $it) {
                    if (! empty($it['product']['name'])) {
                        $names[] = $it['product']['name'];
                    }
                }

                if ($names) {
                    $product = $names[0];
                    if (count($names) > 1) {
                        $product .= ' (+' . (count($names) - 1) . ' ürün)';
                    }
                }
            } else {
                $product = $o['product']['name'] ?? '';
            }

            $out[] = [
                'folder'   => basename($d),
                'offer_no' => $o['offer_no'] ?? basename($d),
                'date'     => $o['date'] ?? '',
                'customer' => ($o['customer']['name'] ?? '') ?: ($o['customer']['company'] ?? ''),
                'product'  => $product,
                'pdf'      => is_file($d . '/offer.pdf'),
                'mtime'    => @filemtime($d) ?: 0,
            ];
        }

        usort($out, static fn ($a, $b) => $b['mtime'] <=> $a['mtime']);

        return $this->respond($out);
    }

    /** open.php: one offer's data (for editing). */
    private function apiOpen(): ResponseInterface
    {
        $f    = TeklifStore::safeName((string) ($this->request->getGet('folder') ?? ''));
        $file = TeklifStore::offersDir() . '/' . $f . '/offer.json';

        if ($f === '' || ! is_file($file)) {
            return $this->respond(['ok' => false, 'message' => 'Bulunamadı']);
        }

        return $this->respond(TeklifStore::jread($file, ['ok' => false]));
    }

    /** delete.php: remove an offer folder (realpath-confined to the offers root). */
    private function apiDelete(): ResponseInterface
    {
        $in   = $this->jsonBody();
        $f    = TeklifStore::safeName((string) ($in['folder'] ?? ''));
        $base = realpath(TeklifStore::offersDir());
        $dir  = ($f !== '' && $base !== false) ? realpath($base . '/' . $f) : false;

        // Containment check added on top of the original: safeName() lets a
        // bare ".." through (basename keeps it), which must never resolve here.
        if ($dir === false || ! is_dir($dir) || ! str_starts_with($dir, $base . DIRECTORY_SEPARATOR)) {
            return $this->respond(['ok' => false, 'message' => 'Bulunamadı']);
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $p) {
            $p->isDir() ? @rmdir($p->getPathname()) : @unlink($p->getPathname());
        }
        @rmdir($dir);

        return $this->respond(['ok' => ! is_dir($dir)]);
    }

    // ------------------------------------------------------------------
    // Small helpers (ported from _common.php's reqBody/respond)
    // ------------------------------------------------------------------

    /** @return array<mixed> decoded JSON request body ([] when empty/invalid) */
    private function jsonBody(): array
    {
        $decoded = json_decode((string) $this->request->getBody(), true);

        return is_array($decoded) ? $decoded : [];
    }

    /** JSON response with the exact headers _common.php used to emit. */
    private function respond(mixed $payload, int $status = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody((string) json_encode($payload, JSON_UNESCAPED_UNICODE));
    }
}
