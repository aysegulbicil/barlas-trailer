<?php

namespace App\Controllers\Qr;

use App\Models\Qr\NotificationModel;

class Notifications extends BaseController
{
    public function index()
    {
        $uid   = (int) session()->get('user_id');
        $model = new NotificationModel();
        $list  = $model->forUser($uid);
        $model->markAllRead($uid);

        return view('qr/notifications/index', ['list' => $list]);
    }
}
