<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

/**
 * Shield grup/izin matrisi — varsayılanlar yeterli (superadmin/admin/user).
 * Rol genişletmesi (personel, musteri, tedarikci) müşteri/personel dünyası
 * fazında buraya eklenecek.
 */
class AuthGroups extends ShieldAuthGroups
{
}
