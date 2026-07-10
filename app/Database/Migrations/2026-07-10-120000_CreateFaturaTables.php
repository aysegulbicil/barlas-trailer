<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Invoice tracking tables for the embedded /fatura tool.
 *
 * Ported from the MariaDB schema of apps/fatura (database `fatura_takip`),
 * adapted to the SQLite default connection:
 *   - ENUM('Gelen','Giden') / ENUM('Ödendi','Ödenmedi') → VARCHAR; values are
 *     whitelisted in App\Controllers\Fatura before every write.
 *   - TIMESTAMP DEFAULT current_timestamp() → DATETIME DEFAULT CURRENT_TIMESTAMP.
 *
 * Creates ONLY firmalar + faturalar; users/auth_* and every other table are
 * out of scope. Safe to re-run: guarded by tableExists + ifNotExists.
 */
class CreateFaturaTables extends Migration
{
    public function up(): void
    {
        // Guard with tableExists so a re-run never fails (SQLite Forge would
        // otherwise still try to CREATE INDEX on an already existing table).
        if (! $this->db->tableExists('firmalar')) {
            $this->forge->addField([
                'id'        => ['type' => 'INTEGER', 'auto_increment' => true],
                'ad'        => ['type' => 'VARCHAR', 'constraint' => 255],
                'olusturma' => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addPrimaryKey('id');
            $this->forge->addUniqueKey('ad');
            $this->forge->createTable('firmalar', true);
        }

        if (! $this->db->tableExists('faturalar')) {
            $this->forge->addField([
                'id'            => ['type' => 'INTEGER', 'auto_increment' => true],
                'firma_id'      => ['type' => 'INTEGER'],
                'fatura_tarihi' => ['type' => 'DATE', 'null' => true],
                'fatura_no'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tur'           => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'Gelen'],
                'tutar'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
                'kdv_orani'     => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 20],
                'kdv_tutari'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
                'toplam'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
                'odeme_sekli'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'odeme_tarihi'  => ['type' => 'DATE', 'null' => true],
                'durum'         => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'Ödenmedi'],
                'aciklama'      => ['type' => 'TEXT', 'null' => true],
                'olusturma'     => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addPrimaryKey('id');
            $this->forge->addKey('firma_id');
            $this->forge->addKey('durum');
            $this->forge->addKey('tur');
            // Enforced only when the connection sets foreignKeys=true (it does,
            // see .env); the controller still deletes child rows explicitly.
            $this->forge->addForeignKey('firma_id', 'firmalar', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('faturalar', true);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('faturalar', true);
        $this->forge->dropTable('firmalar', true);
    }
}
