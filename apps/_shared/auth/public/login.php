<?php

/**
 * Gömülü uygulamalar (fatura/teklif) ortak giriş ekranı.
 * Kimlik kaynağı Jarvis paneliyle AYNIDIR: writable/db/barlas.sqlite →
 * auth_identities (Shield email_password kimliği, bcrypt hash). Yeni
 * kullanıcı/parola işlemleri "spark panel:admin <email> <parola>" ile.
 */

declare(strict_types=1);

require __DIR__ . '/../session.php';

// apps/_shared/auth/public → 4 üst = repo kökü (/var/www/html)
define('APPS_SQLITE', dirname(__DIR__, 4) . '/writable/db/barlas.sqlite');

/** next parametresi yalnız gömülü uygulama yollarına dönebilir (open redirect koruması). */
function apps_next(?string $next): string
{
    if (!is_string($next) || $next === '' || $next[0] !== '/'
        || str_starts_with($next, '//') || str_contains($next, '\\')) {
        return '';
    }
    if ($next === '/apps-auth/go-qr.php') { // hub'ın QR köprüsü — girişten sonra kaldığın yerden
        return $next;
    }
    foreach (['/qr', '/fatura', '/teklif'] as $prefix) {
        if ($next === $prefix || $next === $prefix . '/' || str_starts_with($next, $prefix . '/')) {
            return $next;
        }
    }
    return '';
}

/** IP başına kaba kuvvet freni: 5 hata → 60 sn kilit (dosya tabanlı sayaç). */
function apps_ratelimit_path(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'barlas_apps_login_' . md5($ip) . '.json';
}

function apps_locked(): int
{
    $s = @json_decode((string) @file_get_contents(apps_ratelimit_path()), true) ?: [];
    $until = (int) ($s['until'] ?? 0);
    return $until > time() ? $until - time() : 0;
}

function apps_register_fail(): void
{
    $p = apps_ratelimit_path();
    $s = @json_decode((string) @file_get_contents($p), true) ?: ['fails' => 0, 'until' => 0];
    $s['fails'] = (int) $s['fails'] + 1;
    if ($s['fails'] >= 5) {
        $s = ['fails' => 0, 'until' => time() + 60];
    }
    @file_put_contents($p, json_encode($s));
}

function apps_verify(string $email, string $password): bool
{
    if ($email === '' || $password === '' || !is_file(APPS_SQLITE)) {
        return false;
    }
    try {
        $pdo = new PDO('sqlite:' . APPS_SQLITE, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        // active=1 ŞARTI BİLEREK YOK: kayıt/aktivasyon kapalı olduğundan
        // panel:admin kullanıcıları active=0 kalır ama panele girebilir —
        // Shield ile aynı davran, yalnız yasaklıyı (status=banned) eleriz.
        $st = $pdo->prepare(
            "SELECT i.secret2 AS hash
               FROM auth_identities i
               JOIN users u ON u.id = i.user_id
              WHERE i.type = 'email_password'
                AND lower(i.secret) = lower(:email)
                AND (u.status IS NULL OR u.status <> 'banned')
              LIMIT 1"
        );
        $st->execute([':email' => $email]);
        $hash = (string) ($st->fetchColumn() ?: '');
        return $hash !== '' && password_verify($password, $hash);
    } catch (Throwable $e) {
        return false;
    }
}

$_SESSION['apps_csrf'] ??= bin2hex(random_bytes(16));
$error = '';
$next  = apps_next($_POST['next'] ?? $_GET['next'] ?? null);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && empty($_SESSION['apps_user'])) {
    $lock = apps_locked();
    if (!hash_equals($_SESSION['apps_csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $error = 'Oturum doğrulaması eskidi — yeniden dene.';
    } elseif ($lock > 0) {
        $error = "Çok fazla hatalı deneme. {$lock} sn sonra tekrar dene.";
    } elseif (apps_verify(trim((string) ($_POST['email'] ?? '')), (string) ($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['apps_user'] = strtolower(trim((string) $_POST['email']));
        @unlink(apps_ratelimit_path());
        header('Location: ' . ($next !== '' ? $next : '/apps-auth/'), true, 302);
        exit;
    } else {
        usleep(350000);
        apps_register_fail();
        $error = 'E-posta veya parola hatalı.';
    }
}

// Girişliyken login'in işi yok: next varsa oraya, yoksa kart hub'ına geç.
if (!empty($_SESSION['apps_user'])) {
    header('Location: ' . ($next !== '' ? $next : '/apps-auth/'), true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Barlas · İç Araçlar Girişi</title>
<style>
  :root { color-scheme: dark; }
  * { box-sizing: border-box; margin: 0; }
  body {
    min-height: 100vh; display: grid; place-items: center;
    font: 15px/1.6 system-ui, -apple-system, "Segoe UI", sans-serif;
    background: radial-gradient(1200px 600px at 20% -10%, #1b2559 0%, #0b1026 55%, #070b1c 100%);
    color: #e6e9f5; padding: 24px;
  }
  .card {
    width: 100%; max-width: 380px; background: rgba(15, 20, 45, .85);
    border: 1px solid rgba(120, 140, 220, .25); border-radius: 16px;
    padding: 28px; backdrop-filter: blur(8px);
  }
  h1 { font-size: 19px; margin-bottom: 4px; }
  .sub { color: #94a3c8; font-size: 13px; margin-bottom: 20px; }
  label { display: block; font-size: 13px; color: #aab4d8; margin: 12px 0 4px; }
  input {
    width: 100%; padding: 10px 12px; border-radius: 10px;
    border: 1px solid rgba(120, 140, 220, .3); background: #0d1330; color: #e6e9f5;
    font-size: 15px;
  }
  input:focus { outline: 2px solid #3b82f6; border-color: transparent; }
  button {
    width: 100%; margin-top: 18px; padding: 11px; border: 0; border-radius: 10px;
    background: #2563eb; color: #fff; font-size: 15px; font-weight: 600; cursor: pointer;
  }
  button:hover { background: #1d4ed8; }
  .err {
    margin-top: 14px; padding: 10px 12px; border-radius: 10px; font-size: 13px;
    background: rgba(220, 38, 38, .12); border: 1px solid rgba(248, 113, 113, .35); color: #fca5a5;
  }
</style>
</head>
<body>
<div class="card">
  <h1>İç Araçlar Girişi</h1>
  <p class="sub">QR, Teklif ve Fatura sistemleri için panel hesabınla gir.</p>
  <form method="post" action="/apps-auth/login.php">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['apps_csrf'], ENT_QUOTES) ?>">
    <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES) ?>">
    <label for="email">E-posta</label>
    <input id="email" name="email" type="email" autocomplete="username" required autofocus>
    <label for="password">Parola</label>
    <input id="password" name="password" type="password" autocomplete="current-password" required>
    <button type="submit">Giriş yap</button>
    <?php if ($error !== ''): ?><div class="err"><?= htmlspecialchars($error, ENT_QUOTES) ?></div><?php endif; ?>
  </form>
</div>
</body>
</html>
