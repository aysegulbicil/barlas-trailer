<?php

namespace App\Controllers\Qr\Admin;

use App\Controllers\Qr\BaseController;
use App\Models\Qr\DepartmentModel;
use App\Models\Qr\PositionModel;
use App\Models\Qr\UserModel;

class Positions extends BaseController
{
    public function index()
    {
        return view('qr/admin/positions/index', [
            'positions' => (new PositionModel())->withDepartment(),
        ]);
    }

    public function new()
    {
        return view($this->wantsJson() ? 'qr/admin/positions/_form' : 'qr/admin/positions/form', [
            'position'    => null,
            'departments' => (new DepartmentModel())->ordered(),
        ]);
    }

    public function create()
    {
        if (! $this->validate(['name' => 'required|max_length[120]'])) {
            $msg = 'Pozisyon adı gerekli.';

            return $this->wantsJson() ? $this->jsonError($msg) : redirect()->back()->withInput()->with('error', $msg);
        }

        (new PositionModel())->insert([
            'name'          => (string) $this->request->getPost('name'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'description'   => $this->request->getPost('description') ?: null,
        ]);

        return $this->wantsJson()
            ? $this->jsonOk(qr_url('admin/positions'), 'Pozisyon eklendi.')
            : redirect()->to(qr_url('admin/positions'))->with('message', 'Pozisyon eklendi.');
    }

    public function edit(int $id)
    {
        $position = (new PositionModel())->find($id);
        if ($position === null) {
            return redirect()->to(qr_url('admin/positions'))->with('error', 'Pozisyon bulunamadı.');
        }

        return view($this->wantsJson() ? 'qr/admin/positions/_form' : 'qr/admin/positions/form', [
            'position'    => $position,
            'departments' => (new DepartmentModel())->ordered(),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->validate(['name' => 'required|max_length[120]'])) {
            $msg = 'Pozisyon adı gerekli.';

            return $this->wantsJson() ? $this->jsonError($msg) : redirect()->back()->withInput()->with('error', $msg);
        }

        (new PositionModel())->update($id, [
            'name'          => (string) $this->request->getPost('name'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'description'   => $this->request->getPost('description') ?: null,
        ]);

        return $this->wantsJson()
            ? $this->jsonOk(qr_url('admin/positions'), 'Pozisyon güncellendi.')
            : redirect()->to(qr_url('admin/positions'))->with('message', 'Pozisyon güncellendi.');
    }

    public function delete(int $id)
    {
        $position = (new PositionModel())->find($id);
        if ($position === null) {
            return redirect()->to(qr_url('admin/positions'))->with('error', 'Pozisyon bulunamadı.');
        }

        $userCount = (new UserModel())->where('position_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->to(qr_url('admin/positions'))
                ->with('error', 'Bu pozisyonda ' . $userCount . ' personel var. Önce onları başka pozisyona taşı; pozisyon silinemez.');
        }

        (new PositionModel())->delete($id);

        return redirect()->to(qr_url('admin/positions'))->with('message', 'Pozisyon silindi.');
    }
}
