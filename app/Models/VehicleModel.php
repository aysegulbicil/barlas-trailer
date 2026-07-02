<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * VehicleModel — araç kayıt defteri (QR kimlik kartı + şasi doğrulama).
 */
class VehicleModel extends Model
{
    protected $table         = 'vehicles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'chassis_no', 'public_hash', 'model', 'category_slug', 'year',
        'delivered_at', 'warranty_until', 'notes', 'created_at',
    ];

    public function findByHash(string $hash): ?array
    {
        if (preg_match('/^[a-f0-9]{12,24}$/', $hash) !== 1) {
            return null;
        }

        return $this->where('public_hash', $hash)->first();
    }

    /** Şasi no normalize edilerek aranır (boşluk/tire duyarsız, büyük harf). */
    public function findByChassis(string $chassisNo): ?array
    {
        $normalized = self::normalizeChassis($chassisNo);
        if ($normalized === '') {
            return null;
        }

        return $this->where('chassis_no', $normalized)->first();
    }

    public static function normalizeChassis(string $raw): string
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $raw));
    }
}
