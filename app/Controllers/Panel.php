<?php

namespace App\Controllers;

use App\Models\VehicleModel;
use chillerlan\QRCode\QRCode;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Panel controller — Jarvis komuta merkezi v1 (§11).
 *
 * Shield 'session' filtresi arkasında (Routes.php); ek olarak her istekte
 * superadmin/admin grubu doğrulanır. Ekranlar dosya tabanlı operasyon
 * verisinden beslenir: sabah brifingleri, ajan alarmları (dikkat kuyruğu),
 * lead/teklif kayıtları, SSS adayları, ajan koşu günlükleri ve
 * kill-switch bayrakları.
 *
 * Panel iç araçtır ve Ana Admin Türkçe çalışır — görünümler bilinçli
 * olarak tek dillidir (kamusal 5-dil parite kuralının kapsamı dışında).
 */
class Panel extends BaseController
{
    /** Kill-switch panosunda yönetilen ajanlar. */
    private const AGENTS = ['rates', 'health', 'faq-candidates', 'digest', 'reminders', 'content-factory', 'analyst', 'domain-watch'];

    public function index(): string
    {
        $this->guard();

        $briefings = glob(WRITEPATH . 'data/briefings/*.md') ?: [];
        rsort($briefings);
        $briefing = $briefings === [] ? null : [
            'name' => basename($briefings[0], '.md'),
            'body' => (string) file_get_contents($briefings[0]),
        ];

        return view('pages/panel/dashboard', [
            'briefing' => $briefing,
            'counts'   => [
                'leads'   => count($this->rows('leads')),
                'intents' => count($this->rows('quotes')),
                'asks'    => count($this->rows('ai-logs')),
                'alerts'  => count(glob(WRITEPATH . 'data/alerts/*.json') ?: []),
            ],
            'rates'    => json_decode((string) @file_get_contents(WRITEPATH . 'data/rates.json'), true),
        ]);
    }

    public function alerts(): string
    {
        $this->guard();

        $alerts = [];
        foreach (glob(WRITEPATH . 'data/alerts/*.json') ?: [] as $file) {
            $row = json_decode((string) file_get_contents($file), true);
            if (is_array($row)) {
                $alerts[] = $row;
            }
        }

        return view('pages/panel/alerts', ['alerts' => array_reverse($alerts)]);
    }

    public function archiveAlerts()
    {
        $this->guard();

        $archive = WRITEPATH . 'data/alerts/archive';
        if (! is_dir($archive)) {
            @mkdir($archive, 0775, true);
        }
        foreach (glob(WRITEPATH . 'data/alerts/*.json') ?: [] as $file) {
            @rename($file, $archive . '/' . basename($file));
        }

        return redirect()->to('/panel/alerts');
    }

    public function leads(): string
    {
        $this->guard();

        return view('pages/panel/leads', ['rows' => $this->rows('leads', 50)]);
    }

    public function intents(): string
    {
        $this->guard();

        return view('pages/panel/intents', ['rows' => $this->rows('quotes', 50)]);
    }

    public function faqCandidates(): string
    {
        $this->guard();

        $data = json_decode((string) @file_get_contents(WRITEPATH . 'data/faq-candidates.json'), true);

        return view('pages/panel/faq', ['data' => is_array($data) ? $data : null]);
    }

    public function agents(): string
    {
        $this->guard();

        $agents = [];
        foreach (self::AGENTS as $name) {
            $runs = [];
            $file = WRITEPATH . 'data/agents/' . $name . '/' . date('Y-m') . '.jsonl';
            if (is_file($file)) {
                foreach (array_slice(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [], -10) as $line) {
                    $row = json_decode($line, true);
                    if (is_array($row)) {
                        $runs[] = $row;
                    }
                }
            }
            $agents[$name] = [
                'killed' => is_file(WRITEPATH . 'data/flags/kill-' . $name . '.flag'),
                'runs'   => array_reverse($runs),
            ];
        }

        return view('pages/panel/agents', [
            'agents'    => $agents,
            'killedAll' => is_file(WRITEPATH . 'data/flags/kill-all.flag'),
        ]);
    }

    public function vehicles(): string
    {
        $this->guard();

        $model    = new VehicleModel();
        $vehicles = $model->orderBy('id', 'DESC')->findAll();

        // QR: kamusal kimlik kartı URL'sini gömülü SVG data-URI olarak üret.
        foreach ($vehicles as &$vehicle) {
            $vehicle['card_url'] = base_url('v/' . $vehicle['public_hash']);
            $vehicle['qr']       = (new QRCode())->render($vehicle['card_url']);
        }

        return view('pages/panel/vehicles', [
            'vehicles' => $vehicles,
            'message'  => session()->getFlashdata('panel_msg'),
            'error'    => session()->getFlashdata('panel_err'),
        ]);
    }

    public function addVehicle()
    {
        $this->guard();

        $chassis = VehicleModel::normalizeChassis((string) $this->request->getPost('chassis_no'));
        $name    = trim((string) $this->request->getPost('model'));

        if ($chassis === '' || mb_strlen($chassis) < 5 || $name === '') {
            return redirect()->to('/panel/vehicles')->with('panel_err', 'Şasi no (en az 5 karakter) ve model zorunludur.');
        }

        $model = new VehicleModel();
        if ($model->findByChassis($chassis) !== null) {
            return redirect()->to('/panel/vehicles')->with('panel_err', 'Bu şasi numarası zaten kayıtlı: ' . $chassis);
        }

        $year = (int) $this->request->getPost('year');

        $model->insert([
            'chassis_no'     => $chassis,
            'public_hash'    => bin2hex(random_bytes(8)),
            'model'          => $name,
            'year'           => $year > 1980 ? $year : null,
            'delivered_at'   => $this->request->getPost('delivered_at') ?: null,
            'warranty_until' => $this->request->getPost('warranty_until') ?: null,
            'notes'          => trim((string) $this->request->getPost('notes')) ?: null,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/panel/vehicles')->with('panel_msg', 'Araç kaydedildi: ' . $chassis);
    }

    public function toggleAgent()
    {
        $this->guard();

        $name = (string) $this->request->getPost('agent');
        if ($name !== 'all' && ! in_array($name, self::AGENTS, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $dir = WRITEPATH . 'data/flags';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $flag = $dir . '/kill-' . $name . '.flag';
        if (is_file($flag)) {
            @unlink($flag);
        } else {
            @file_put_contents($flag, date('c') . ' — panelden durduruldu' . PHP_EOL);
        }

        return redirect()->to('/panel/agents');
    }

    /**
     * İçerik Fabrikası onay kuyruğu (§7.6): ajan taslakları
     * writable/data/content/drafts/ altında bekler; onay haberi
     * app/Data/content/news/tr/{slug}.md olarak yayınlar.
     */
    public function content(): string
    {
        $this->guard();

        $read = static function (string $dir): array {
            $rows = [];
            foreach (glob(WRITEPATH . 'data/content/' . $dir . '/*.json') ?: [] as $file) {
                $row = json_decode((string) file_get_contents($file), true);
                if (is_array($row)) {
                    $rows[] = $row;
                }
            }
            usort($rows, static fn (array $a, array $b): int => strcmp((string) ($b['created'] ?? ''), (string) ($a['created'] ?? '')));

            return $rows;
        };

        return view('pages/panel/content', [
            'drafts'    => $read('drafts'),
            'published' => array_slice($read('published'), 0, 20),
            'message'   => session()->getFlashdata('panel_msg'),
            'error'     => session()->getFlashdata('panel_err'),
        ]);
    }

    public function decideContent()
    {
        $this->guard();

        $id     = (string) $this->request->getPost('id');
        $action = (string) $this->request->getPost('action');

        if (preg_match('/^[a-z0-9-]+$/', $id) !== 1 || ! in_array($action, ['approve', 'reject'], true)) {
            return redirect()->to('/panel/content')->with('panel_err', 'Geçersiz istek.');
        }

        $draftFile = WRITEPATH . 'data/content/drafts/' . $id . '.json';
        $draft     = json_decode((string) @file_get_contents($draftFile), true);

        if (! is_array($draft)) {
            return redirect()->to('/panel/content')->with('panel_err', 'Taslak bulunamadı: ' . $id);
        }

        if ($action === 'reject') {
            $this->moveDraft($draftFile, 'rejected', $draft);

            return redirect()->to('/panel/content')->with('panel_msg', 'Taslak reddedildi: ' . $id);
        }

        // Onay: markdown olarak yayınla (kamusal /news, MarkdownContent motoru).
        $dir = APPPATH . 'Data/content/news/tr';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $markdown = "---\n"
            . 'title: ' . str_replace(["\r", "\n"], ' ', (string) $draft['title']) . "\n"
            . 'description: ' . str_replace(["\r", "\n"], ' ', (string) $draft['description']) . "\n"
            . 'updated: ' . date('Y-m-d') . "\n"
            . "---\n\n"
            . (string) $draft['body'] . "\n";

        if (@file_put_contents($dir . '/' . $id . '.md', $markdown, LOCK_EX) === false) {
            return redirect()->to('/panel/content')->with('panel_err', 'Yayın dosyası yazılamadı (dizin izni?): ' . $dir);
        }

        $this->moveDraft($draftFile, 'published', $draft + ['published_at' => date('c')]);

        // Sayfa önbelleği yeni haberi 1 saat gizlemesin.
        cache()->clean();

        return redirect()->to('/panel/content')->with('panel_msg', 'Yayınlandı: /tr/news/' . $id);
    }

    /** Taslağı karar klasörüne taşır (drafts → published/rejected). */
    private function moveDraft(string $draftFile, string $target, array $payload): void
    {
        $dir = WRITEPATH . 'data/content/' . $target;
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents(
            $dir . '/' . basename($draftFile),
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n",
            LOCK_EX
        );
        @unlink($draftFile);
    }

    /** Aylık JSONL akışından satırları okur (en yeni önce). */
    private function rows(string $stream, int $limit = 500): array
    {
        $file = WRITEPATH . $stream . '/' . date('Y-m') . '.jsonl';
        if (! is_file($file)) {
            return [];
        }

        $rows = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $row = json_decode($line, true);
            if (is_array($row)) {
                $rows[] = $row;
            }
        }

        return array_slice(array_reverse($rows), 0, $limit);
    }

    /** Oturum + yetkili grup zorunlu (filtre 'session' + grup denetimi). */
    private function guard(): void
    {
        $user = auth()->user();
        if ($user === null || ! ($user->inGroup('superadmin') || $user->inGroup('admin'))) {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
