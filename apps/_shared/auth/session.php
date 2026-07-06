<?php

/**
 * Gömülü uygulamalar (fatura/teklif) ortak oturum başlangıcı.
 * Çerez adı BARLASAPPS: ana sitenin ci_session'ı ve qr uygulamasının
 * qr_session'ı ile aynı hostta çakışmadan yaşar.
 */

declare(strict_types=1);

if (PHP_SAPI === 'cli') {
    return;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Caddy arkasında HTTPS, Apache'ye düz HTTP olarak gelir — proxy başlığına bak.
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_name('BARLASAPPS');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $secure,
        'samesite' => 'Lax',
    ]);
    session_start();
}
