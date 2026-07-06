<?php

declare(strict_types=1);

require __DIR__ . '/../session.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

header('Location: /apps-auth/login.php', true, 302);
exit;
