<?php

/**
 * Statik dosya bekçisi: .htaccess .php olmayan her isteği buraya çevirir.
 * Oturum denetimi otomatik — bu dosya da auto_prepend guard kapsamında,
 * girişsiz istek buraya ulaşamaz. Geriye güvenli dosya servisi kalır.
 */

declare(strict_types=1);

$base = __DIR__;
$p    = str_replace('\\', '/', (string) ($_GET['p'] ?? ''));

if ($p === '' || $p === '/') {
    header('Location: /teklif/ui/index.html', true, 302);
    exit;
}

// Nokta ile başlayan segment (.htaccess, .git, ..) ve null bayt reddi.
if (str_contains($p, "\0") || preg_match('#(^|/)\.#', $p)) {
    http_response_code(404);
    exit;
}

$full = realpath($base . DIRECTORY_SEPARATOR . $p);
if ($full === false || !is_file($full) || !str_starts_with($full, $base . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    exit;
}

$mime = [
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
][strtolower(pathinfo($full, PATHINFO_EXTENSION))] ?? null;

if ($mime === null) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . (string) filesize($full));
// Teklif çıktıları kişisel veri — tarayıcı/ara önbelleğe girmesin.
header(str_starts_with($p, 'offers/') ? 'Cache-Control: no-store' : 'Cache-Control: private, max-age=3600');
readfile($full);
exit;
