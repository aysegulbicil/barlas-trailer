<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\Auth as ShieldAuth;

/**
 * Shield auth yapılandırması — Jarvis paneli (§4.3 / §11).
 *
 * Varsayılanların üzerine yalnız yönlendirmeler yazılır; kayıt (register)
 * rotası hiç açılmaz (Routes.php'de except ile) — kullanıcılar yalnızca
 * "php spark panel:admin" komutuyla oluşturulur.
 */
class Auth extends ShieldAuth
{
    public array $redirects = [
        'register'          => '/panel',
        'login'             => '/panel',
        'logout'            => '/login',
        'force_reset'       => '/',
        'permission_denied' => '/',
        'group_denied'      => '/',
    ];

    /** Magic-link kapalı (rotası da açılmıyor); giriş yalnız e-posta+parola. */
    public bool $allowMagicLinkLogins = false;

    /** Kayıt kapalı: kullanıcı yalnız "spark panel:admin" ile oluşturulur. */
    public bool $allowRegistration = false;
}
