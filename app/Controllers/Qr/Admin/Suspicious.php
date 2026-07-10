<?php

namespace App\Controllers\Qr\Admin;

use App\Controllers\Qr\BaseController;
use App\Models\Qr\SuspiciousEventModel;

class Suspicious extends BaseController
{
    public function index()
    {
        return view('qr/admin/suspicious/index', [
            'events' => (new SuspiciousEventModel())->recent(200),
        ]);
    }
}
