<?php

namespace App\Controllers\Qr\Admin;

use App\Controllers\Qr\BaseController;
use App\Models\Qr\SettingModel;

class Settings extends BaseController
{
    public function index()
    {
        return view('qr/admin/settings', [
            'settings' => (new SettingModel())->allMerged(),
        ]);
    }

    public function save()
    {
        $model  = new SettingModel();
        $fields = [
            'company_name', 'work_mode', 'fixed_start', 'fixed_end', 'grace_in', 'grace_out',
            'currency', 'daily_hours', 'workdays_per_month', 'overtime_multiplier',
        ];

        foreach ($fields as $field) {
            $model->setValue($field, (string) $this->request->getPost($field));
        }

        return redirect()->to(qr_url('admin/settings'))->with('message', 'Ayarlar kaydedildi.');
    }
}
