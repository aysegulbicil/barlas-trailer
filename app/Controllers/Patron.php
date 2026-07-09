<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

/**
 * İç Araçlar giriş + hub'ı — ROTA sürümü.
 *
 * Bu controller, Apache Alias'ına bağlı `apps/_shared/auth/public/*.php`
 * dosyalarının CodeIgniter karşılığıdır. Docker/Apache ortamında istekler
 * hâlâ Alias üzerinden o düz PHP dosyalarına düşer (bu controller devreye
 * girmez); Alias'ı OLMAYAN ortamlarda (ör. Hostinger paylaşımlı hosting)
 * ise /patron artık bu rotayla çalışır — 404 yerine giriş ekranı gelir.
 *
 * Kimlik kaynağı panelle AYNIDIR: default DB bağlantısı barlas.sqlite →
 * auth_identities (Shield email_password, bcrypt). Oturum, panelin Shield
 * oturumundan bilinçli AYRIDIR: yalnız `apps_user` bayrağı tutulur.
 */
class Patron extends BaseController
{
    /** Girişten sonra dönülebilecek güvenli yollar (open-redirect koruması). */
    private function safeNext(?string $next): string
    {
        if (! is_string($next) || $next === '' || $next[0] !== '/'
            || str_starts_with($next, '//') || str_contains($next, '\\')) {
            return '';
        }
        // Hub'ın QR köprüsü — girişten sonra kaldığın yerden devam.
        if ($next === '/patron/go-qr' || $next === '/patron/go-qr.php') {
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
    private function ratelimitPath(): string
    {
        $ip = $this->request->getIPAddress() ?: 'unknown';

        return WRITEPATH . 'data/apps-login-' . md5($ip) . '.json';
    }

    private function locked(): int
    {
        $s     = @json_decode((string) @file_get_contents($this->ratelimitPath()), true) ?: [];
        $until = (int) ($s['until'] ?? 0);

        return $until > time() ? $until - time() : 0;
    }

    private function registerFail(): void
    {
        $p = $this->ratelimitPath();
        $s = @json_decode((string) @file_get_contents($p), true) ?: ['fails' => 0, 'until' => 0];
        $s['fails'] = (int) $s['fails'] + 1;
        if ($s['fails'] >= 5) {
            $s = ['fails' => 0, 'until' => time() + 60];
        }
        @file_put_contents($p, json_encode($s));
    }

    /** Panel (Shield) hesabıyla doğrula — login.php'deki apps_verify ile aynı sorgu. */
    private function verify(string $email, string $password): bool
    {
        if ($email === '' || $password === '') {
            return false;
        }
        try {
            // active=1 ŞARTI BİLEREK YOK: panel:admin kullanıcıları active=0
            // kalır ama panele girebilir — Shield ile aynı davran, yalnız
            // yasaklıyı (status=banned) eleriz.
            $row = db_connect()
                ->table('auth_identities i')
                ->select('i.secret2 AS hash')
                ->join('users u', 'u.id = i.user_id')
                ->where('i.type', 'email_password')
                ->where('lower(i.secret)', strtolower($email))
                ->groupStart()
                    ->where('u.status IS NULL', null, false)
                    ->orWhere('u.status !=', 'banned')
                ->groupEnd()
                ->limit(1)
                ->get()
                ->getRow();

            $hash = (string) ($row->hash ?? '');

            return $hash !== '' && password_verify($password, $hash);
        } catch (Throwable $e) {
            return false;
        }
    }

    /** Üç kartlı hub — girişsizse login'e. */
    public function index(): RedirectResponse|string
    {
        $user = (string) (session('apps_user') ?? '');
        if ($user === '') {
            return redirect()->route('patron.login');
        }

        return view('patron/hub', ['user' => $user]);
    }

    /** Giriş ekranı + POST doğrulaması. CSRF, global filtre tarafından uygulanır. */
    public function login(): RedirectResponse|string
    {
        $next = $this->safeNext(
            $this->request->getPost('next') ?? $this->request->getGet('next')
        );

        // Zaten girişliyse login'in işi yok.
        if (session('apps_user')) {
            return redirect()->to($next !== '' ? site_url(ltrim($next, '/')) : site_url('patron'));
        }

        $error = '';

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $lock = $this->locked();
            if ($lock > 0) {
                $error = "Çok fazla hatalı deneme. {$lock} sn sonra tekrar dene.";
            } elseif ($this->verify(
                trim((string) $this->request->getPost('email')),
                (string) $this->request->getPost('password')
            )) {
                session()->regenerate(true);
                session()->set('apps_user', strtolower(trim((string) $this->request->getPost('email'))));
                @unlink($this->ratelimitPath());

                return redirect()->to($next !== '' ? site_url(ltrim($next, '/')) : site_url('patron'));
            } else {
                usleep(350000);
                $this->registerFail();
                $error = 'E-posta veya parola hatalı.';
            }
        }

        return view('patron/login', ['error' => $error, 'next' => $next]);
    }

    /** Çıkış — yalnız apps oturumunu temizle, login'e dön. */
    public function logout(): RedirectResponse
    {
        session()->remove('apps_user');

        return redirect()->route('patron.login');
    }

    /** Hub → QR köprüsü: taze SSO token'ı üret, /qr/sso'ya yönlen. */
    public function goQr(): RedirectResponse
    {
        if (! session('apps_user')) {
            return redirect()->to(site_url('patron/login') . '?next=' . rawurlencode('/patron/go-qr'));
        }

        // SSO token yardımcıları apps/_shared'da; QR tarafı AYNI dosyayla doğrular.
        require_once dirname(APPPATH) . '/apps/_shared/auth/sso.php';

        $token = apps_sso_issue((string) session('apps_user'));

        return redirect()->to($token !== '' ? site_url('qr/sso') . '?t=' . urlencode($token) : site_url('qr/login'));
    }
}
