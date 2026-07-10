<?php

namespace App\Models\Qr;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $DBGroup       = 'qr';
    protected $table         = 'departments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'description'];

    public function ordered(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
}
