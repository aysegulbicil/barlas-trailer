<?php

/**
 * Gömülü uygulamalar arası tek-giriş (SSO) token yardımcıları.
 *
 * Hub'daki QR kartı /apps-auth/go-qr.php'ye gider; orada BARLASAPPS oturumu
 * doğrulanıp bu dosyayla kısa ömürlü imzalı bir token üretilir ve /qr/sso'ya
 * yönlenilir. QR tarafındaki Sso controller'ı AYNI dosyayı require ederek
 * token'ı doğrular ve kendi oturumunu açar. Anahtar writable/data altında
 * durur (git dışı); ilk kullanımda otomatik üretilir.
 */

declare(strict_types=1);

if (!defined('APPS_SSO_TTL')) {
    define('APPS_SSO_TTL', 60); // saniye — köprü anında üretip anında tükettiği için kısa
    // apps/_shared/auth → 3 üst = repo kökü
    define('APPS_SSO_KEY_FILE', dirname(__DIR__, 3) . '/writable/data/apps-sso.key');
}

/** İmza anahtarını oku; yoksa üret. Üretilemiyorsa '' döner (köprü zarifçe QR login'ine düşer). */
function apps_sso_key(): string
{
    $key = @file_get_contents(APPS_SSO_KEY_FILE);
    if (is_string($key) && strlen(trim($key)) >= 32) {
        return trim($key);
    }
    $key = bin2hex(random_bytes(32));
    if (@file_put_contents(APPS_SSO_KEY_FILE, $key, LOCK_EX) === false) {
        return '';
    }
    @chmod(APPS_SSO_KEY_FILE, 0600);

    return $key;
}

function apps_sso_b64(string $bin): string
{
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

/** Girişli kullanıcının e-postası için tek kullanımlık kısa ömürlü token üret. */
function apps_sso_issue(string $email): string
{
    $key = apps_sso_key();
    if ($key === '' || $email === '') {
        return '';
    }
    $payload = apps_sso_b64(json_encode(['e' => $email, 'x' => time() + APPS_SSO_TTL]));

    return $payload . '.' . apps_sso_b64(hash_hmac('sha256', $payload, $key, true));
}

/** Token'ı doğrula; geçerliyse e-postayı, değilse null döner. */
function apps_sso_verify(string $token): ?string
{
    $key = apps_sso_key();
    if ($key === '' || substr_count($token, '.') !== 1) {
        return null;
    }
    [$payload, $sig] = explode('.', $token, 2);
    $expected = apps_sso_b64(hash_hmac('sha256', $payload, $key, true));
    if (!hash_equals($expected, $sig)) {
        return null;
    }
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')) ?: '', true);
    if (!is_array($data) || (int) ($data['x'] ?? 0) < time()) {
        return null;
    }
    $email = (string) ($data['e'] ?? '');

    return $email !== '' ? $email : null;
}
