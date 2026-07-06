<?php

/**
 * Hub → QR köprüsü. BARLASAPPS oturumu girişliyse taze bir SSO token'ı
 * üretip /qr/sso'ya yönlendirir; QR tarafı token'la kendi oturumunu açar.
 * Token üretilemezse (anahtar yazılamadı vb.) QR'ın kendi login'ine düşer.
 */

declare(strict_types=1);

require __DIR__ . '/../session.php';
require __DIR__ . '/../sso.php';

if (empty($_SESSION['apps_user'])) {
    header('Location: /patron/login.php?next=' . rawurlencode('/patron/go-qr.php'), true, 302);
    exit;
}

$token = apps_sso_issue((string) $_SESSION['apps_user']);

header('Location: ' . ($token !== '' ? '/qr/sso?t=' . urlencode($token) : '/qr/login'), true, 302);
exit;
