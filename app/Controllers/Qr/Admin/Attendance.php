<?php

namespace App\Controllers\Qr\Admin;

use App\Controllers\Qr\BaseController;
use App\Models\Qr\AttendanceLogModel;
use App\Models\Qr\LocationModel;
use App\Models\Qr\UserModel;

/**
 * Admin devam kaydı düzeltme: geçmiş giriş/çıkışları görüntüle, düzelt, elle ekle;
 * çıkış yapılmamış (unutulan) açık kayıtları tespit edip tamamla.
 */
class Attendance extends BaseController
{
    public function index()
    {
        $model = new AttendanceLogModel();

        return view('qr/admin/attendance/index', [
            'logs'     => $model->recentDetailed(300),
            'openLogs' => $model->openCheckIns(),
        ]);
    }

    public function new()
    {
        return view($this->wantsJson() ? 'qr/admin/attendance/_form' : 'qr/admin/attendance/form', $this->formData(null));
    }

    public function edit(int $id)
    {
        $log = (new AttendanceLogModel())->findDetailed($id);
        if ($log === null) {
            return redirect()->to(qr_url('admin/attendance'))->with('error', 'Kayıt bulunamadı.');
        }

        return view($this->wantsJson() ? 'qr/admin/attendance/_form' : 'qr/admin/attendance/form', $this->formData($log));
    }

    public function create()
    {
        if ($error = $this->validatePayload()) {
            return $this->wantsJson() ? $this->jsonError($error) : redirect()->back()->withInput()->with('error', $error);
        }

        (new AttendanceLogModel())->insert($this->payload(false));

        return $this->wantsJson()
            ? $this->jsonOk(qr_url('admin/attendance'), 'Devam kaydı eklendi.')
            : redirect()->to(qr_url('admin/attendance'))->with('message', 'Devam kaydı eklendi.');
    }

    public function update(int $id)
    {
        $log = (new AttendanceLogModel())->find($id);
        if ($log === null) {
            $msg = 'Kayıt bulunamadı.';

            return $this->wantsJson() ? $this->jsonError($msg) : redirect()->to(qr_url('admin/attendance'))->with('error', $msg);
        }
        if ($error = $this->validatePayload()) {
            return $this->wantsJson() ? $this->jsonError($error) : redirect()->back()->withInput()->with('error', $error);
        }

        (new AttendanceLogModel())->update($id, $this->payload(true));

        return $this->wantsJson()
            ? $this->jsonOk(qr_url('admin/attendance'), 'Devam kaydı güncellendi.')
            : redirect()->to(qr_url('admin/attendance'))->with('message', 'Devam kaydı güncellendi.');
    }

    public function delete(int $id)
    {
        (new AttendanceLogModel())->delete($id);

        return redirect()->to(qr_url('admin/attendance'))->with('message', 'Devam kaydı silindi.');
    }

    // ---- yardımcılar ----

    private function formData(?array $log): array
    {
        return [
            'log'         => $log,
            'employees'   => (new UserModel())->where('role', 'employee')->orderBy('full_name', 'ASC')->findAll(),
            'locations'   => (new LocationModel())->orderBy('name', 'ASC')->findAll(),
            'prefillUser' => (int) ($this->request->getGet('user_id') ?? 0),
            'prefillType' => $this->request->getGet('type') === 'out' ? 'out' : 'in',
        ];
    }

    private function eventAt(): ?string
    {
        $raw = trim((string) $this->request->getPost('event_at'));
        if ($raw === '') {
            return null;
        }
        $ts = strtotime(str_replace('T', ' ', $raw));

        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    private function validatePayload(): ?string
    {
        $userId = (int) $this->request->getPost('user_id');
        $type   = (string) $this->request->getPost('type');

        if ($userId <= 0 || (new UserModel())->find($userId) === null) {
            return 'Geçerli bir personel seç.';
        }
        if (! in_array($type, ['in', 'out'], true)) {
            return 'Yön (giriş/çıkış) geçersiz.';
        }
        if ($this->eventAt() === null) {
            return 'Geçerli bir tarih/saat gir.';
        }

        return null;
    }

    private function payload(bool $isUpdate): array
    {
        $locationId = $this->request->getPost('location_id');
        $data = [
            'user_id'     => (int) $this->request->getPost('user_id'),
            'type'        => (string) $this->request->getPost('type'),
            'event_at'    => $this->eventAt(),
            'location_id' => $locationId ? (int) $locationId : null,
            'note'        => trim((string) $this->request->getPost('note')) ?: null,
        ];
        if (! $isUpdate) {
            $data['source']     = 'manual';
            $data['ip_address'] = $this->request->getIPAddress();
        }

        return $data;
    }
}
