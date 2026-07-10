<?php

namespace App\Models\Qr;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $DBGroup       = 'qr';
    protected $table         = 'locations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'code', 'name', 'qr_mode', 'token_secret', 'is_active',
        'geo_lat', 'geo_lng', 'geo_radius_m', 'enforce_geo',
    ];

    public function findByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }
}
