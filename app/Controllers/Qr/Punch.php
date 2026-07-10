<?php

namespace App\Controllers\Qr;

/**
 * Tarama kapisi. Admin veya bu oturumda QR okutmus personeli panele (/qr/dashboard) yollar;
 * okutmamis personele "Once QR okut" ekranini gosterir.
 */
class Punch extends BaseController
{
    public function index()
    {
        if (session()->get('role') === 'admin' || session()->get('scan_unlocked')) {
            return redirect()->to(qr_url('dashboard'));
        }

        return view('qr/punch', ['title' => 'QR Gerekli']);
    }
}
