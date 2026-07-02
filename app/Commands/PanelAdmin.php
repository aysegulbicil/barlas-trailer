<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Shield\Entities\User;

/**
 * Jarvis paneli yöneticisi oluşturur/günceller (kayıt rotası bilinçli
 * kapalı olduğundan kullanıcı açmanın TEK yolu budur).
 *
 * Kullanım:
 *   php spark panel:admin <email> <parola>
 *
 * Var olan e-posta verilirse parolası güncellenir; yoksa superadmin
 * grubunda yeni kullanıcı açılır.
 */
class PanelAdmin extends BaseCommand
{
    protected $group       = 'Panel';
    protected $name        = 'panel:admin';
    protected $description = 'Jarvis paneli için superadmin kullanıcı oluşturur veya parolasını günceller.';
    protected $usage       = 'panel:admin <email> <parola>';

    public function run(array $params)
    {
        helper(['auth', 'setting']); // CLI bağlamında otomatik yüklenmez

        $email    = (string) ($params[0] ?? '');
        $password = (string) ($params[1] ?? '');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 10) {
            CLI::error('Kullanım: panel:admin <email> <parola>  (parola en az 10 karakter)');

            return;
        }

        $users    = auth()->getProvider();
        $existing = $users->findByCredentials(['email' => $email]);

        if ($existing !== null) {
            $existing->fill(['password' => $password]);
            $users->save($existing);
            $user = $users->findById($existing->id);
            CLI::write('Parola güncellendi: ' . $email, 'green');
        } else {
            $user = new User([
                'username' => 'admin',
                'email'    => $email,
                'password' => $password,
            ]);
            $users->save($user);
            $user = $users->findById($users->getInsertID());
            CLI::write('Superadmin oluşturuldu: ' . $email, 'green');
        }

        // İdempotent: grup ve aktivasyon her koşuda garanti edilir.
        if (! $user->inGroup('superadmin')) {
            $user->addGroup('superadmin');
        }
        if (! $user->isActivated()) {
            $user->activate();
        }

        CLI::write('Grup: superadmin ✓  Aktif ✓', 'green');
    }
}
