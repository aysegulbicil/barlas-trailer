<?php

namespace App\Controllers;

use App\Models\VehicleModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Vehicle controller — QR Araç Kimlik Kartı (§5.1) + Şasi Doğrulama (§5.9).
 *
 * card():  /v/{hash} — araca yapıştırılan QR'ın hedefi. Locale önekinsiz
 *          (QR kısa kalsın); metinler ziyaretçinin çözümlenen diliyle gelir.
 * verify/check(): /{locale}/verify — şasi no ile orijinallik + garanti
 *          sorgusu (throttle + bal kabı korumalı).
 */
class Vehicle extends BaseController
{
    public function card(string $hash): string
    {
        $vehicle = (new VehicleModel())->findByHash($hash);
        if ($vehicle === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('pages/vehicle/card', [
            'metaTitle'       => lang('Vehicle.card_title') . ' — ' . $vehicle['model'],
            'metaDescription' => lang('Vehicle.meta_description'),
            'vehicle'         => $vehicle,
            'warranty'        => $this->warrantyState($vehicle),
        ]);
    }

    public function verify(): string
    {
        return $this->renderVerify(null, null);
    }

    public function check()
    {
        // Bal kabı: botlar doldurur, sessizce boş sonuç ver.
        if (trim((string) $this->request->getPost('website')) !== '') {
            return $this->renderVerify('notfound', null);
        }

        if (service('throttler')->check(md5('verify-' . $this->request->getIPAddress()), 10, MINUTE) === false) {
            return $this->renderVerify('throttled', null);
        }

        $vehicle = (new VehicleModel())->findByChassis((string) $this->request->getPost('chassis'));

        return $this->renderVerify($vehicle === null ? 'notfound' : 'found', $vehicle);
    }

    private function renderVerify(?string $result, ?array $vehicle): string
    {
        return view('pages/vehicle/verify', [
            'metaTitle'       => lang('Vehicle.meta_title'),
            'metaDescription' => lang('Vehicle.meta_description'),
            'result'          => $result,
            'vehicle'         => $vehicle,
            'warranty'        => $vehicle === null ? null : $this->warrantyState($vehicle),
        ]);
    }

    /** @return string 'active' | 'expired' | 'none' */
    private function warrantyState(array $vehicle): string
    {
        $until = (string) ($vehicle['warranty_until'] ?? '');
        if ($until === '') {
            return 'none';
        }

        return $until >= date('Y-m-d') ? 'active' : 'expired';
    }
}
