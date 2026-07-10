<?php

namespace App\Models\Qr;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $DBGroup       = 'qr';
    protected $table         = 'shifts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name', 'start_time', 'end_time',
        'grace_in_minutes', 'grace_out_minutes',
        'crosses_midnight', 'workdays',
    ];

    public function ordered(): array
    {
        return $this->orderBy('start_time', 'ASC')->findAll();
    }
}
