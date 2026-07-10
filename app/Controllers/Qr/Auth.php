<?php

namespace App\Controllers\Qr;

use App\Models\Qr\UserModel;

class Auth extends BaseController
{
    /**
     * Session keys owned by the QR app. The session is the shared main-site
     * ci_session; logout must remove ONLY these keys (never destroy the whole
     * session, which also carries the patron hub `apps_user` and Shield data).
     */
    private const SESSION_KEYS = [
        'user_id',
        'full_name',
        'role',
        'scan_unlocked',
        'scan_context',
        'redirect_url',
    ];

    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to(qr_url('dashboard'));
        }

        return view('qr/auth/login');
    }

    public function attemptLogin()
    {
        // Brute-force korumasi: IP basina dakikada en fazla 5 deneme.
        $throttler = service('throttler');
        if ($throttler->check(md5('login-' . $this->request->getIPAddress()), 5, MINUTE) === false) {
            return redirect()->back()->withInput()->with('error', 'Cok fazla giris denemesi. Lutfen bir dakika sonra tekrar dene.');
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $user = (new UserModel())->findByUsername($username);

        if ($user === null || ! $user['is_active'] || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Kullanıcı adı veya parola hatalı.');
        }

        // Session fixation korumasi: giriste oturum kimligini yenile.
        session()->regenerate(true);

        session()->set([
            'user_id'   => (int) $user['id'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
        ]);

        $redirect = session()->get('redirect_url');
        session()->remove('redirect_url');

        return redirect()->to($redirect ?: qr_url('dashboard'));
    }

    public function logout()
    {
        // Do NOT call session()->destroy(): the session is shared with the
        // patron hub and the Jarvis panel. Drop only the QR-owned keys.
        session()->remove(self::SESSION_KEYS);

        return redirect()->to(qr_url('login'))->with('message', 'Çıkış yapıldı.');
    }
}
