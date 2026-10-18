<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkOrderTables extends Migration
{
    public function up()
    {
        // 1. roles
        $this->forge->addField([
            'id_role' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addKey('id_role', true);
        $this->forge->createTable('roles', true);

        // 2. departemen
        $this->forge->addField([
            'id_departemen' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_departemen' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_dept' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
        ]);
        $this->forge->addKey('id_departemen', true);
        $this->forge->createTable('departemen', true);

        // 3. users
        $this->forge->addField([
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_lengkap' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'no_whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'id_departemen' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_role' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_aktif' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addForeignKey('id_departemen', 'departemen', 'id_departemen', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_role', 'roles', 'id_role', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('users', true);

        // 4. kategori_wo
        $this->forge->addField([
            'id_kategori' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id_kategori', true);
        $this->forge->createTable('kategori_wo', true);

        // 5. status_wo
        $this->forge->addField([
            'id_status' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_status' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id_status', true);
        $this->forge->createTable('status_wo', true);

        // 6. prioritas_sla
        $this->forge->addField([
            'id_prioritas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tingkat_prioritas' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'target_jam' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id_prioritas', true);
        $this->forge->createTable('prioritas_sla', true);

        // 7. aset_hotel
        $this->forge->addField([
            'id_aset' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_aset' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'nama_aset' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'id_departemen' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'lokasi_spesifik' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'qr_code_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'kondisi' => [
                'type' => 'ENUM',
                'constraint' => ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                'default' => 'Baik',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_aset', true);
        $this->forge->addForeignKey('id_departemen', 'departemen', 'id_departemen', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('aset_hotel', true);

        // 8. inventaris_barang
        $this->forge->addField([
            'id_barang' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_barang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_barang' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'satuan' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'harga_satuan' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
        ]);
        $this->forge->addKey('id_barang', true);
        $this->forge->createTable('inventaris_barang', true);

        // 9. work_order
        $this->forge->addField([
            'id_wo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_wo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'judul_wo' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_kategori' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_status' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_prioritas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_departemen' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_aset' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'foto_trouble' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'foto_bukti_selesai' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'tanggal_ajuan' => [
                'type' => 'DATETIME',
            ],
            'deadline' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reject_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'total_biaya_sparepart' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
            'durasi_menit_pengerjaan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_wo', true);
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_kategori', 'kategori_wo', 'id_kategori', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_status', 'status_wo', 'id_status', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_prioritas', 'prioritas_sla', 'id_prioritas', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_departemen', 'departemen', 'id_departemen', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_aset', 'aset_hotel', 'id_aset', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('assigned_to', 'users', 'id_user', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('verified_by', 'users', 'id_user', 'SET NULL', 'SET NULL');
        $this->forge->createTable('work_order', true);

        // 10. wo_spareparts
        $this->forge->addField([
            'id_usage' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_wo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_barang' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'jumlah' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'harga_subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
        ]);
        $this->forge->addKey('id_usage', true);
        $this->forge->addForeignKey('id_wo', 'work_order', 'id_wo', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang', 'inventaris_barang', 'id_barang', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('wo_spareparts', true);

        // 11. log_audit
        $this->forge->addField([
            'id_log' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'aktivitas' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'modul' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'timestamp' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_log', true);
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'SET NULL', 'SET NULL');
        $this->forge->createTable('log_audit', true);

        // 12. reset_password_token
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'expired_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('reset_password_token', true);
    }

    public function down()
    {
        $this->forge->dropTable('reset_password_token', true);
        $this->forge->dropTable('log_audit', true);
        $this->forge->dropTable('wo_spareparts', true);
        $this->forge->dropTable('work_order', true);
        $this->forge->dropTable('inventaris_barang', true);
        $this->forge->dropTable('aset_hotel', true);
        $this->forge->dropTable('prioritas_sla', true);
        $this->forge->dropTable('status_wo', true);
        $this->forge->dropTable('kategori_wo', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('departemen', true);
        $this->forge->dropTable('roles', true);
    }
}
