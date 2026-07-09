<?php

/**
 * İç Araçlar giriş ekranı (rota sürümü). Görsel dil, Apache Alias'lı
 * apps/_shared/auth/public/login.php ile birebir aynıdır; yalnız form
 * hedefi CI4 rotasına ve CSRF alanı CI4 token'ına uyarlanmıştır.
 *
 * @var string $error
 * @var string $next
 */
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
  <form method="post" action="<?= site_url('patron/login') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="next" value="<?= esc($next, 'attr') ?>">
    <label for="email">E-posta</label>
    <input id="email" name="email" type="email" autocomplete="username" required autofocus>
    <label for="password">Parola</label>
    <input id="password" name="password" type="password" autocomplete="current-password" required>
    <button type="submit">Giriş yap</button>
    <?php if ($error !== ''): ?><div class="err"><?= esc($error) ?></div><?php endif; ?>
  </form>
</div>
</body>
</html>
