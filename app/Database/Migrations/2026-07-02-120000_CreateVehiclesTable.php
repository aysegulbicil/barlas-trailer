<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Araç kayıt defteri — QR Araç Kimlik Kartı + şasi doğrulama (§5.1/§5.9).
 * Kayıtlar Jarvis panelinden girilir; kamusal /v/{public_hash} sayfası ve
 * şasi doğrulama formu buradan okur.
 */
class CreateVehiclesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INTEGER', 'auto_increment' => true],
            'chassis_no'     => ['type' => 'VARCHAR', 'constraint' => 40],
            'public_hash'    => ['type' => 'VARCHAR', 'constraint' => 24],
            'model'          => ['type' => 'VARCHAR', 'constraint' => 160],
            'category_slug'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'year'           => ['type' => 'INTEGER', 'null' => true],
            'delivered_at'   => ['type' => 'DATE', 'null' => true],
            'warranty_until' => ['type' => 'DATE', 'null' => true],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('chassis_no');
        $this->forge->addUniqueKey('public_hash');
        $this->forge->createTable('vehicles');
    }

    public function down(): void
    {
        $this->forge->dropTable('vehicles');
    }
}
