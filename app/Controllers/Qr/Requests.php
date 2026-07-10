<?php

namespace App\Controllers\Qr;

use App\Models\Qr\AdvanceRequestModel;
use App\Models\Qr\LeaveRequestModel;
use App\Models\Qr\LeaveTypeModel;
use App\Services\Qr\NotificationService;

class Requests extends BaseController
{
    public function index()
    {
        $uid = (int) session()->get('user_id');

        return view('qr/requests/index', [
            'leaves'   => (new LeaveRequestModel())->forUser($uid),
            'advances' => (new AdvanceRequestModel())->forUser($uid),
            'types'    => (new LeaveTypeModel())->ordered(),
        ]);
    }

    public function createLeave()
    {
        if (! $this->validate(['leave_type_id' => 'required', 'start_date' => 'required|valid_date', 'end_date' => 'required|valid_date'])) {
            return redirect()->back()->with('error', 'İzin bilgilerini kontrol et.');
        }

        $start = (string) $this->request->getPost('start_date');
        $end   = (string) $this->request->getPost('end_date');
        if (strtotime($end) < strtotime($start)) {
            return redirect()->back()->with('error', 'Bitiş tarihi başlangıçtan önce olamaz.');
        }

        $uid  = (int) session()->get('user_id');
        $days = (strtotime($end) - strtotime($start)) / 86400 + 1;

        (new LeaveRequestModel())->insert([
            'user_id'       => $uid,
            'leave_type_id' => (int) $this->request->getPost('leave_type_id'),
            'start_date'    => $start,
            'end_date'      => $end,
            'days'          => $days,
            'reason'        => $this->request->getPost('reason') ?: null,
            'status'        => 'pending',
        ]);

        (new NotificationService())->notifyAdmins('leave', 'Yeni izin talebi: ' . session()->get('full_name'), qr_url('admin/requests'));

        return redirect()->to(qr_url('requests'))->with('message', 'İzin talebin oluşturuldu.');
    }

    public function createAdvance()
    {
        if (! $this->validate(['amount' => 'required|decimal|greater_than[0]'])) {
            return redirect()->back()->with('error', 'Geçerli bir tutar gir.');
        }

        $uid = (int) session()->get('user_id');
        (new AdvanceRequestModel())->insert([
            'user_id'      => $uid,
            'amount'       => (float) $this->request->getPost('amount'),
            'reason'       => $this->request->getPost('reason') ?: null,
            'status'       => 'pending',
            'period_year'  => (int) date('Y'),
            'period_month' => (int) date('m'),
        ]);

        (new NotificationService())->notifyAdmins('advance', 'Yeni avans talebi: ' . session()->get('full_name'), qr_url('admin/requests'));

        return redirect()->to(qr_url('requests'))->with('message', 'Avans talebin oluşturuldu.');
    }
}
