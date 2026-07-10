<?php

namespace App\Controllers\Qr;

use App\Models\Qr\AttendanceLogModel;
use App\Services\Qr\AttendanceCalculator;
use App\Services\Qr\ShiftResolver;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('user_id');
        $model  = new AttendanceLogModel();
        $today  = date('Y-m-d');

        $todayLogs = $model->forUserBetween($userId, $today, $today);
        $shift     = (new ShiftResolver())->forUser($userId);
        $summary   = (new AttendanceCalculator())->computeDay($todayLogs, $shift, $today);

        return view('qr/dashboard', [
            'checkedIn' => $model->isCheckedIn($userId),
            'scan'      => session()->get('scan_context'),
            'canPunch'  => scan_is_fresh(session()->get('scan_context')),
            'today'     => $summary,
        ]);
    }
}
