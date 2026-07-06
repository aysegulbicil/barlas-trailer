<?php

/**
 * Gömülü uygulama giriş bekçisi — deploy/apache-apps.conf içindeki
 * php_admin_value auto_prepend_file hedefi. /fatura ve /teklif altındaki
 * HER .php isteğinden önce koşar (teklif'in statik dosyaları da gate.php
 * üzerinden geçtiği için bu kapsamdadır). /patron bu kapsamın dışındadır.
 */

declare(strict_types=1);

if (defined('BARLAS_APPS_GUARD')) {
    return;
}
define('BARLAS_APPS_GUARD', 1);

if (PHP_SAPI === 'cli') {
    return;
}

require __DIR__ . '/session.php';

if (!empty($_SESSION['apps_user'])) {
    return; // girişli — istenen betiğe devam
}

$uri = $_SERVER['REQUEST_URI'] ?? '/';

// API çağrıları yönlendirme izleyemez — JSON 401 dön.
$wantsJson = str_contains($uri, '/server/api/')
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

if ($wantsJson) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'auth_required']);
    exit;
}

header('Location: /patron/login.php?next=' . rawurlencode($uri), true, 302);
exit;
