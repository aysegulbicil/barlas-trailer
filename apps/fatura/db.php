<?php
/**
 * Veritabanı bağlantısı ve ortak yardımcılar.
 * İlk açılışta `fatura_takip` veritabanını ve firmalar + faturalar
 * tablolarını otomatik kurar (XAMPP / MySQL varsayılanları).
 */

declare(strict_types=1);
mb_internal_encoding('UTF-8');

// ---- Bağlantı ayarları ----
// Gömülü kurulumda (barlas-trailer compose) FATURA_DB_* ortam değişkenleri
// web servisinde tanımlıdır; XAMPP'ta hiçbiri yoktur ve eski varsayılanlar
// (127.0.0.1 / root) devreye girer. const fonksiyon çağıramadığı için define.
define('DB_HOST', getenv('FATURA_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('FATURA_DB_PORT') ?: '3306');
define('DB_NAME', getenv('FATURA_DB_NAME') ?: 'fatura_takip');
define('DB_USER', getenv('FATURA_DB_USER') ?: 'root');
define('DB_PASS', getenv('FATURA_DB_PASS') ?: '');

/**
 * Tekil PDO bağlantısı döndürür; gerekiyorsa veritabanı/tablo kurulumunu yapar.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    try {
        // Önce doğrudan bağlan: gömülü kurulumda veritabanı hazır gelir ve
        // `apps` kullanıcısının global CREATE yetkisi yoktur.
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
    } catch (PDOException $ex) {
        try {
            // 1049 (unknown database) → XAMPP tarzı ilk kurulum: oluşturup yeniden bağlan.
            if ((int) ($ex->errorInfo[1] ?? 0) !== 1049) {
                throw $ex;
            }
            $root = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                $opt
            );
            $root->exec(
                'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '`
                 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
            );
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
        } catch (PDOException $ex2) {
            http_response_code(500);
            die(
                '<div style="font:15px/1.6 system-ui;max-width:640px;margin:60px auto;padding:24px;'
                . 'border:1px solid #fca5a5;background:#fef2f2;border-radius:12px;color:#7f1d1d">'
                . '<h2 style="margin:0 0 8px">Veritabanına bağlanılamadı</h2>'
                . '<p>MySQL/MariaDB servisinin çalıştığından emin ol '
                . '(Docker: <b>barlas-db</b> konteyneri · XAMPP: Control Panel → MySQL → Start).</p>'
                . '<pre style="white-space:pre-wrap;background:#fff;padding:10px;border-radius:8px;'
                . 'border:1px solid #fecaca">' . htmlspecialchars($ex2->getMessage()) . '</pre></div>'
            );
        }
    }

    // ---- Tablolar ----
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS firmalar (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ad VARCHAR(190) NOT NULL,
            UNIQUE KEY uq_firma_ad (ad)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS faturalar (
            id INT AUTO_INCREMENT PRIMARY KEY,
            firma_id INT NOT NULL,
            fatura_tarihi DATE NULL,
            fatura_no VARCHAR(100) NULL,
            tur ENUM('Gelen','Giden') NOT NULL DEFAULT 'Gelen',
            tutar DECIMAL(15,2) NOT NULL DEFAULT 0,
            kdv_orani DECIMAL(6,2) NOT NULL DEFAULT 0,
            kdv_tutari DECIMAL(15,2) NOT NULL DEFAULT 0,
            toplam DECIMAL(15,2) NOT NULL DEFAULT 0,
            odeme_sekli VARCHAR(100) NULL,
            odeme_tarihi DATE NULL,
            durum ENUM('Ödendi','Ödenmedi') NOT NULL DEFAULT 'Ödenmedi',
            aciklama TEXT NULL,
            KEY idx_firma (firma_id),
            KEY idx_durum (durum),
            CONSTRAINT fk_fatura_firma FOREIGN KEY (firma_id)
                REFERENCES firmalar(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    return $pdo;
}

/** HTML kaçışı. */
function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Türk Lirası biçimi: 1.234,56 ₺ */
function para($n): string
{
    return number_format((float) $n, 2, ',', '.') . ' ₺';
}

/** Tarihi gg.aa.yyyy biçiminde gösterir; boşsa "—". */
function tarih($d): string
{
    if (!$d) return '—';
    $t = strtotime((string) $d);
    return $t ? date('d.m.Y', $t) : e($d);
}

/**
 * Numaralı sayfalama bağlantıları üretir.
 * $base: mevcut sorgu parametreleri (page hariç) dizisi.
 */
function pagination_html(int $current, int $total, array $base = []): string
{
    if ($total <= 1) return '';

    $url = function (int $p) use ($base): string {
        $q = array_merge($base, ['page' => $p]);
        return '?' . http_build_query($q);
    };

    $out = '<nav class="pagination">';
    $out .= $current > 1
        ? '<a href="' . e($url($current - 1)) . '">‹</a>'
        : '<span class="disabled">‹</span>';

    $win = 2;
    $start = max(1, $current - $win);
    $end   = min($total, $current + $win);

    if ($start > 1) {
        $out .= '<a href="' . e($url(1)) . '">1</a>';
        if ($start > 2) $out .= '<span class="gap">…</span>';
    }
    for ($p = $start; $p <= $end; $p++) {
        $out .= $p === $current
            ? '<span class="current">' . $p . '</span>'
            : '<a href="' . e($url($p)) . '">' . $p . '</a>';
    }
    if ($end < $total) {
        if ($end < $total - 1) $out .= '<span class="gap">…</span>';
        $out .= '<a href="' . e($url($total)) . '">' . $total . '</a>';
    }

    $out .= $current < $total
        ? '<a href="' . e($url($current + 1)) . '">›</a>'
        : '<span class="disabled">›</span>';
    $out .= '</nav>';
    return $out;
}
