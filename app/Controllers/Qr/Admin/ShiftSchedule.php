<?php

namespace App\Controllers\Qr\Admin;

use App\Controllers\Qr\BaseController;
use App\Models\Qr\ShiftAssignmentModel;
use App\Models\Qr\ShiftModel;
use App\Models\Qr\UserModel;

class ShiftSchedule extends BaseController
{
    public function index()
    {
        $userId    = (int) ($this->request->getGet('user_id') ?: 0);
        $weekStart = (string) ($this->request->getGet('week') ?: date('Y-m-d', strtotime('monday this week')));

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = date('Y-m-d', strtotime($weekStart . ' +' . $i . ' day'));
        }

        $assignments = $userId ? (new ShiftAssignmentModel())->forUserBetween($userId, $days[0], $days[6]) : [];

        return view('qr/admin/shifts/schedule', [
            'employees'   => (new UserModel())->employees(),
            'shifts'      => (new ShiftModel())->ordered(),
            'userId'      => $userId,
            'weekStart'   => $weekStart,
            'days'        => $days,
            'assignments' => $assignments,
            'prevWeek'    => date('Y-m-d', strtotime($weekStart . ' -7 day')),
            'nextWeek'    => date('Y-m-d', strtotime($weekStart . ' +7 day')),
        ]);
    }

    public function save()
    {
        $userId = (int) $this->request->getPost('user_id');
        $week   = (string) $this->request->getPost('week');
        $shifts = $this->request->getPost('shift');

        if ($userId && is_array($shifts)) {
            $model = new ShiftAssignmentModel();
            foreach ($shifts as $date => $shiftId) {
                if ($shiftId) {
                    $model->assign($userId, $date, (int) $shiftId);
                }
            }
        }

        return redirect()->to(qr_url('admin/shift-schedule') . '?user_id=' . $userId . '&week=' . $week)
            ->with('message', 'Vardiya planı kaydedildi.');
    }
}
