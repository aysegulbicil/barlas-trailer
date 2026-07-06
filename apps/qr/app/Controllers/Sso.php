<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Ortak giriş kapısından (hub) gelen tek-giriş noktası.
 * /patron/go-qr.php kısa ömürlü imzalı bir token ile buraya yönlendirir;
 * token apps/_shared/auth/sso.php ile doğrulanır ve QR oturumu açılır.
 * QR'ın kendi /login ekranı yedek kapı olarak aynen çalışmaya devam eder.
 */
class Sso extends BaseController
{
    public function enter()
    {
        // Zaten girişliyse token'a bakmadan içeri al.
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        require_once dirname(ROOTPATH) . '/_shared/auth/sso.php';

        $email = apps_sso_verify((string) $this->request->getGet('t'));
        if ($email === null) {
            return redirect()->to('/login')->with('error', 'Ortak giriş bağlantısı geçersiz ya da süresi doldu — buradan girebilirsin.');
        }

        $users = new UserModel();

        // Hub hesabının e-postasıyla eşleşen personel varsa kendi yetkisiyle girer;
        // eşleşme yoksa hub kullanıcısı yönetici kabul edilir (ilk aktif admin).
        $user = $users->where('is_active', 1)
            ->groupStart()->where('email', $email)->orWhere('username', $email)->groupEnd()
            ->first();

        if ($user === null) {
            $user = $users->where(['is_active' => 1, 'role' => 'admin'])->orderBy('id', 'ASC')->first();
        }

        if ($user === null) {
            return redirect()->to('/login')->with('error', 'Ortak girişe bağlanacak aktif kullanıcı bulunamadı.');
        }

        session()->regenerate(true);

        session()->set([
            'user_id'   => (int) $user['id'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
        ]);

        return redirect()->to('/dashboard');
    }
}
