<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * File-backed store + helpers for the embedded offer builder (/teklif).
 *
 * Straight port of apps/teklif/server/api/_common.php (kept untouched as a
 * reference/backup). Only two things changed on purpose:
 *   - the data/offers roots now live under writable/data/teklif/ so nothing
 *     dynamic sits in the public webroot;
 *   - openFolder() (Windows Explorer launcher) was NOT ported — a web
 *     request must never spawn desktop processes.
 *
 * Everything else (slugify, folder naming, counter semantics, headless
 * Chrome/Edge PDF generation) matches the original byte-for-byte behavior.
 */
final class TeklifStore
{
    /** Root that holds products/templates/company/counter JSON files. */
    public static function dataDir(): string
    {
        return rtrim(WRITEPATH, '/\\') . '/data/teklif/data';
    }

    /** Root that holds one folder per saved offer. */
    public static function offersDir(): string
    {
        return rtrim(WRITEPATH, '/\\') . '/data/teklif/offers';
    }

    /**
     * Read a JSON file; return $default when the file is missing.
     *
     * @return mixed
     */
    public static function jread(string $file, mixed $default = null): mixed
    {
        return is_file($file) ? json_decode((string) file_get_contents($file), true) : $default;
    }

    /** Write a value as pretty-printed JSON. */
    public static function jwrite(string $file, mixed $value): void
    {
        file_put_contents($file, json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /** Folder/file name safety (blocks path traversal). */
    public static function safeName(mixed $name): string
    {
        $name = basename((string) $name);

        return (string) preg_replace('/[^A-Za-z0-9._\- ]/u', '', $name);
    }

    /** Customer name -> file-friendly slug. */
    public static function slugify(mixed $value): string
    {
        $s  = trim((string) $value);
        $tr = ['ı' => 'i', 'İ' => 'i', 'ğ' => 'g', 'Ğ' => 'g', 'ü' => 'u', 'Ü' => 'u', 'ş' => 's', 'Ş' => 's', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c'];
        $s  = strtr($s, $tr);
        $s  = (string) preg_replace('/[^A-Za-z0-9]+/', '-', $s);
        $s  = trim($s, '-');

        return $s !== '' ? substr($s, 0, 40) : 'customer';
    }

    /** DD.MM.YYYY -> YYYY-MM-DD (for the offer folder name). */
    public static function dateISO(mixed $value): string
    {
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', trim((string) $value), $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        return date('Y-m-d');
    }

    /** Find a headless-print capable browser (Linux: Chromium/Chrome, Windows: Edge/Chrome). */
    public static function findBrowser(): ?string
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            foreach (['/usr/bin/chromium', '/usr/bin/chromium-browser', '/usr/bin/google-chrome'] as $exe) {
                if (is_file($exe)) {
                    return $exe;
                }
            }

            return null;
        }

        $pf   = getenv('PROGRAMFILES') ?: 'C:\\Program Files';
        $pf86 = getenv('PROGRAMFILES(X86)') ?: 'C:\\Program Files (x86)';
        $la   = getenv('LOCALAPPDATA') ?: '';

        $candidates = [
            $pf86 . '\\Microsoft\\Edge\\Application\\msedge.exe',
            $pf . '\\Microsoft\\Edge\\Application\\msedge.exe',
            $pf . '\\Google\\Chrome\\Application\\chrome.exe',
            $pf86 . '\\Google\\Chrome\\Application\\chrome.exe',
            $la . '\\Google\\Chrome\\Application\\chrome.exe',
        ];

        foreach ($candidates as $exe) {
            if ($exe && is_file($exe)) {
                return $exe;
            }
        }

        return null;
    }

    /**
     * offer.html -> offer.pdf (silent, via Chrome/Edge headless).
     *
     * Returns false when exec/browser is unavailable or printing fails —
     * callers degrade gracefully (the UI falls back to the print dialog).
     */
    public static function makePdf(string $htmlPath, string $pdfPath): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $exe = self::findBrowser();
        if ($exe === null) {
            return false;
        }

        $profile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'barlas_pdf_' . uniqid();

        if (PHP_OS_FAMILY !== 'Windows') {
            // Docker/Linux: www-data's HOME is not writable (HOME=/tmp required),
            // the sandbox must stay off inside the container and /dev/shm is
            // only 64 MB, hence --disable-dev-shm-usage.
            @unlink($pdfPath);
            $cmd = 'HOME=/tmp ' . escapeshellarg($exe)
                 . ' --headless --no-sandbox --disable-gpu --disable-dev-shm-usage'
                 . ' --no-pdf-header-footer --no-margins'
                 . ' --user-data-dir=' . escapeshellarg($profile)
                 . ' --print-to-pdf=' . escapeshellarg($pdfPath)
                 . ' ' . escapeshellarg('file://' . $htmlPath);
            @exec($cmd . ' 2>&1', $out, $rc);

            return is_file($pdfPath) && filesize($pdfPath) > 800;
        }

        // Windows (XAMPP dev) branch — kept as in the original.
        $url      = 'file:///' . str_replace('\\', '/', $htmlPath);
        $variants = [
            '--headless=new --disable-gpu --no-pdf-header-footer',
            '--headless --disable-gpu --print-to-pdf-no-header',
        ];

        foreach ($variants as $flags) {
            @unlink($pdfPath);
            $cmd = '"' . $exe . '" ' . $flags . ' --no-margins --user-data-dir="' . $profile . '" '
                 . '--print-to-pdf="' . $pdfPath . '" "' . $url . '"';
            @exec('cmd /c "' . $cmd . '" 2>&1', $out, $rc);
            if (is_file($pdfPath) && filesize($pdfPath) > 800) {
                return true;
            }
        }

        return false;
    }
}
