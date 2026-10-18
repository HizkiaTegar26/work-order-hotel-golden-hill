<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. ROLES
        $roles = [
            ['id_role' => 1, 'nama_role' => 'Admin'],
            ['id_role' => 2, 'nama_role' => 'Kepala Departemen'],
            ['id_role' => 3, 'nama_role' => 'Staf'],
        ];
        $db->table('roles')->emptyTable();
        $db->table('roles')->insertBatch($roles);

        // 2. DEPARTEMEN
        $departemen = [
            ['id_departemen' => 1, 'nama_departemen' => 'Engineering & Maintenance', 'kode_dept' => 'ENG'],
            ['id_departemen' => 2, 'nama_departemen' => 'IT & Systems', 'kode_dept' => 'IT'],
            ['id_departemen' => 3, 'nama_departemen' => 'Housekeeping', 'kode_dept' => 'HK'],
            ['id_departemen' => 4, 'nama_departemen' => 'Front Office', 'kode_dept' => 'FO'],
            ['id_departemen' => 5, 'nama_departemen' => 'Food & Beverage', 'kode_dept' => 'FB'],
        ];
        $db->table('departemen')->emptyTable();
        $db->table('departemen')->insertBatch($departemen);

        // 3. USERS
        $now = date('Y-m-d H:i:s');
        $users = [
            [
                'id_user' => 1,
                'nama_lengkap' => 'Administrator Hotel',
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'email' => 'admin@goldenhill.com',
                'no_whatsapp' => '081234567890',
                'id_departemen' => 2,
                'id_role' => 1,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 2,
                'nama_lengkap' => 'Hizkia Tegar Kurniawan',
                'username' => 'hizkia',
                'password' => password_hash('hizkia123', PASSWORD_BCRYPT),
                'email' => 'hizkiategar@student.ub.ac.id',
                'no_whatsapp' => '081234567899',
                'id_departemen' => 2,
                'id_role' => 1,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 3,
                'nama_lengkap' => 'Ir. Budi Santoso (Kadep ENG)',
                'username' => 'kadep_eng',
                'password' => password_hash('kadep123', PASSWORD_BCRYPT),
                'email' => 'kadep.eng@goldenhill.com',
                'no_whatsapp' => '081298765431',
                'id_departemen' => 1,
                'id_role' => 2,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 4,
                'nama_lengkap' => 'Joko Prasetyo (Staf ENG)',
                'username' => 'staf_eng',
                'password' => password_hash('staf123', PASSWORD_BCRYPT),
                'email' => 'joko.eng@goldenhill.com',
                'no_whatsapp' => '081298765432',
                'id_departemen' => 1,
                'id_role' => 3,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 5,
                'nama_lengkap' => 'Rian Pratama, S.Kom (Kadep IT)',
                'username' => 'kadep_it',
                'password' => password_hash('kadep123', PASSWORD_BCRYPT),
                'email' => 'kadep.it@goldenhill.com',
                'no_whatsapp' => '081298765433',
                'id_departemen' => 2,
                'id_role' => 2,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 6,
                'nama_lengkap' => 'Fajar Ramadhan (Staf IT)',
                'username' => 'staf_it',
                'password' => password_hash('staf123', PASSWORD_BCRYPT),
                'email' => 'fajar.it@goldenhill.com',
                'no_whatsapp' => '081298765434',
                'id_departemen' => 2,
                'id_role' => 3,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 7,
                'nama_lengkap' => 'Siti Aminah (Kadep HK)',
                'username' => 'kadep_hk',
                'password' => password_hash('kadep123', PASSWORD_BCRYPT),
                'email' => 'kadep.hk@goldenhill.com',
                'no_whatsapp' => '081298765435',
                'id_departemen' => 3,
                'id_role' => 2,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 8,
                'nama_lengkap' => 'Dewi Lestari (Staf HK)',
                'username' => 'staf_hk',
                'password' => password_hash('staf123', PASSWORD_BCRYPT),
                'email' => 'dewi.hk@goldenhill.com',
                'no_whatsapp' => '081298765436',
                'id_departemen' => 3,
                'id_role' => 3,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_user' => 9,
                'nama_lengkap' => 'Reza Firmansyah (Staf FO)',
                'username' => 'staf_fo',
                'password' => password_hash('staf123', PASSWORD_BCRYPT),
                'email' => 'reza.fo@goldenhill.com',
                'no_whatsapp' => '081298765437',
                'id_departemen' => 4,
                'id_role' => 3,
                'status_aktif' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $db->table('users')->emptyTable();
        $db->table('users')->insertBatch($users);

        // 4. KATEGORI WO
        $kategori = [
            ['id_kategori' => 1, 'nama_kategori' => 'Maintenance'],
            ['id_kategori' => 2, 'nama_kategori' => 'IT Support'],
            ['id_kategori' => 3, 'nama_kategori' => 'Housekeeping Service'],
            ['id_kategori' => 4, 'nama_kategori' => 'General Repair'],
        ];
        $db->table('kategori_wo')->emptyTable();
        $db->table('kategori_wo')->insertBatch($kategori);

        // 5. STATUS WO
        $status = [
            ['id_status' => 1, 'nama_status' => 'Baru'],
            ['id_status' => 2, 'nama_status' => 'Accepted'],
            ['id_status' => 3, 'nama_status' => 'Ditolak'],
            ['id_status' => 4, 'nama_status' => 'Ditugaskan'],
            ['id_status' => 5, 'nama_status' => 'Proses'],
            ['id_status' => 6, 'nama_status' => 'Selesai'],
            ['id_status' => 7, 'nama_status' => 'Menunggu Konfirmasi'],
            ['id_status' => 8, 'nama_status' => 'Escalated'],
        ];
        $db->table('status_wo')->emptyTable();
        $db->table('status_wo')->insertBatch($status);

        // 6. PRIORITAS SLA
        $prioritas = [
            ['id_prioritas' => 1, 'tingkat_prioritas' => 'Low', 'target_jam' => 24],
            ['id_prioritas' => 2, 'tingkat_prioritas' => 'Medium', 'target_jam' => 12],
            ['id_prioritas' => 3, 'tingkat_prioritas' => 'High', 'target_jam' => 4],
            ['id_prioritas' => 4, 'tingkat_prioritas' => 'Emergency', 'target_jam' => 1],
        ];
        $db->table('prioritas_sla')->emptyTable();
        $db->table('prioritas_sla')->insertBatch($prioritas);

        // 7. INVENTARIS BARANG
        $barang = [
            ['id_barang' => 1, 'kode_barang' => 'BRG-001', 'nama_barang' => 'Freon R410A Kaleng', 'stok' => 25, 'satuan' => 'Tabung', 'harga_satuan' => 450000.00],
            ['id_barang' => 2, 'kode_barang' => 'BRG-002', 'nama_barang' => 'Lampu LED Downlight Philips 12W', 'stok' => 80, 'satuan' => 'Pcs', 'harga_satuan' => 65000.00],
            ['id_barang' => 3, 'kode_barang' => 'BRG-003', 'nama_barang' => 'Kabel UTP Belden Cat6 (Rol)', 'stok' => 150, 'satuan' => 'Meter', 'harga_satuan' => 8500.00],
            ['id_barang' => 4, 'kode_barang' => 'BRG-004', 'nama_barang' => 'Stop Kontak Panasonic 2 Gang', 'stok' => 40, 'satuan' => 'Pcs', 'harga_satuan' => 38000.00],
            ['id_barang' => 5, 'kode_barang' => 'BRG-005', 'nama_barang' => 'Baterai Alkaline AAA Remote', 'stok' => 120, 'satuan' => 'Pcs', 'harga_satuan' => 7500.00],
            ['id_barang' => 6, 'kode_barang' => 'BRG-006', 'nama_barang' => 'Filter AC Split Daikin', 'stok' => 30, 'satuan' => 'Pcs', 'harga_satuan' => 85000.00],
            ['id_barang' => 7, 'kode_barang' => 'BRG-007', 'nama_barang' => 'Keran Air Wastafel Kuningan', 'stok' => 20, 'satuan' => 'Pcs', 'harga_satuan' => 125000.00],
            ['id_barang' => 8, 'kode_barang' => 'BRG-008', 'nama_barang' => 'MCB Schneider Electric 16A', 'stok' => 35, 'satuan' => 'Pcs', 'harga_satuan' => 72000.00],
        ];
        $db->table('inventaris_barang')->emptyTable();
        $db->table('inventaris_barang')->insertBatch($barang);

        // 8. ASET HOTEL (Generating QR Codes)
        $assetsData = [
            ['id_aset' => 1, 'kode_aset' => 'AST-ENG-001', 'nama_aset' => 'AC Central Chiller Lobby Utama', 'id_departemen' => 1, 'lokasi_spesifik' => 'Lobby Utama Lt. 1', 'kondisi' => 'Baik'],
            ['id_aset' => 2, 'kode_aset' => 'AST-ENG-002', 'nama_aset' => 'Passenger Elevator Lift 1 Tower A', 'id_departemen' => 1, 'lokasi_spesifik' => 'Tower A Koridor Utama', 'kondisi' => 'Baik'],
            ['id_aset' => 3, 'kode_aset' => 'AST-ENG-003', 'nama_aset' => 'Genset Silent Cummins 250kVA', 'id_departemen' => 1, 'lokasi_spesifik' => 'Ruang Power House Basement', 'kondisi' => 'Baik'],
            ['id_aset' => 4, 'kode_aset' => 'AST-IT-001', 'nama_aset' => 'Server Main Data Center HPE ProLiant', 'id_departemen' => 2, 'lokasi_spesifik' => 'Ruang Server Lt. 2', 'kondisi' => 'Baik'],
            ['id_aset' => 5, 'kode_aset' => 'AST-IT-002', 'nama_aset' => 'Cisco Core Switch Catalyst 3850', 'id_departemen' => 2, 'lokasi_spesifik' => 'Ruang Server Lt. 2 Rack A', 'kondisi' => 'Baik'],
            ['id_aset' => 6, 'kode_aset' => 'AST-IT-003', 'nama_aset' => 'Access Point Aruba AP-505 Ballroom', 'id_departemen' => 2, 'lokasi_spesifik' => 'Grand Ballroom Lt. 1', 'kondisi' => 'Baik'],
            ['id_aset' => 7, 'kode_aset' => 'AST-HK-001', 'nama_aset' => 'Mesin Polisher Lantai Nilfisk', 'id_departemen' => 3, 'lokasi_spesifik' => 'Janitor Lt. 1 Depan Ballroom', 'kondisi' => 'Baik'],
            ['id_aset' => 8, 'kode_aset' => 'AST-HK-002', 'nama_aset' => 'Vacuum Cleaner Wet & Dry Karcher', 'id_departemen' => 3, 'lokasi_spesifik' => 'Janitor Lt. 2 Koridor Timur', 'kondisi' => 'Rusak Ringan'],
        ];

        $qrDir = FCPATH . 'uploads/qrcodes/';
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0777, true);
        }

        $pngWriter = new PngWriter();
        $db->table('aset_hotel')->emptyTable();

        foreach ($assetsData as $item) {
            $qrFile = 'qr_' . str_replace('-', '_', strtolower($item['kode_aset'])) . '.png';
            $qrPath = $qrDir . $qrFile;
            
            // Format QR Content: Kode Aset or JSON data for scanning
            $qr = new QrCode($item['kode_aset']);
            $result = $pngWriter->write($qr);
            $result->saveToFile($qrPath);

            $item['qr_code_path'] = 'uploads/qrcodes/' . $qrFile;
            $item['created_at'] = $now;
            $db->table('aset_hotel')->insert($item);
        }

        // 9. WORK ORDERS (Realistic sample records for full testing & dashboard analytics)
        $db->table('wo_spareparts')->emptyTable();
        $db->table('work_order')->emptyTable();

        $wo1_date = date('Y-m-d H:i:s', strtotime('-2 days'));
        $wo1_deadline = date('Y-m-d H:i:s', strtotime('-2 days + 4 hours'));
        $wo1_verified = date('Y-m-d H:i:s', strtotime('-2 days + 15 minutes'));
        $db->table('work_order')->insert([
            'id_wo' => 1,
            'kode_wo' => 'WO-20261016-0001',
            'judul_wo' => 'AC Chiller Lobby Utama Mengeluarkan Hawa Hangat',
            'deskripsi' => 'Pengunjung di area lobby mengeluhkan suhu ruangan gerah. Setelah dicek indikator suhu menunjukkan 27 derajat celcius.',
            'id_user' => 9, // Staf FO
            'id_kategori' => 1, // Maintenance
            'id_status' => 6, // Selesai
            'id_prioritas' => 3, // High (4 Jam)
            'id_departemen' => 1, // ENG
            'id_aset' => 1, // AC Central
            'foto_trouble' => null,
            'foto_bukti_selesai' => null,
            'tanggal_ajuan' => $wo1_date,
            'deadline' => $wo1_deadline,
            'assigned_to' => 4, // Staf ENG
            'verified_by' => 3, // Kadep ENG
            'verified_at' => $wo1_verified,
            'reject_note' => null,
            'total_biaya_sparepart' => 535000.00,
            'durasi_menit_pengerjaan' => 145,
        ]);
        // Spare parts for WO 1
        $db->table('wo_spareparts')->insert([
            'id_wo' => 1,
            'id_barang' => 1, // Freon
            'jumlah' => 1,
            'harga_subtotal' => 450000.00,
        ]);
        $db->table('wo_spareparts')->insert([
            'id_wo' => 1,
            'id_barang' => 6, // Filter AC
            'jumlah' => 1,
            'harga_subtotal' => 85000.00,
        ]);

        // WO 2: In Progress / Ditugaskan (Active SLA timer)
        $wo2_date = date('Y-m-d H:i:s', strtotime('-1 hours'));
        $wo2_deadline = date('Y-m-d H:i:s', strtotime('+3 hours'));
        $db->table('work_order')->insert([
            'id_wo' => 2,
            'kode_wo' => 'WO-20261018-0002',
            'judul_wo' => 'Access Point Grand Ballroom Tidak Memancarkan SSID',
            'deskripsi' => 'Sinyal Wi-Fi tamu ballroom terputus mendadak saat persiapan seminar. Perlu pengecekan kabel PoE switch dan konfigurasi.',
            'id_user' => 8, // Staf HK
            'id_kategori' => 2, // IT Support
            'id_status' => 4, // Ditugaskan
            'id_prioritas' => 3, // High (4 jam)
            'id_departemen' => 2, // IT
            'id_aset' => 6, // AP Ballroom
            'foto_trouble' => null,
            'foto_bukti_selesai' => null,
            'tanggal_ajuan' => $wo2_date,
            'deadline' => $wo2_deadline,
            'assigned_to' => 6, // Staf IT
            'verified_by' => 5, // Kadep IT
            'verified_at' => $wo2_date,
            'reject_note' => null,
            'total_biaya_sparepart' => 0.00,
            'durasi_menit_pengerjaan' => null,
        ]);

        // WO 3: Baru (Awaiting Verification from Kadep)
        $wo3_date = date('Y-m-d H:i:s', strtotime('-20 minutes'));
        $wo3_deadline = date('Y-m-d H:i:s', strtotime('+23 hours 40 minutes'));
        $db->table('work_order')->insert([
            'id_wo' => 3,
            'kode_wo' => 'WO-20261018-0003',
            'judul_wo' => 'Lampu Koridor Kamar Lt. 3 Berkedip',
            'deskripsi' => 'Dua unit lampu downlight di depan kamar 308 berkedip-kedip, perlu penggantian bohlam LED baru.',
            'id_user' => 8, // Staf HK
            'id_kategori' => 4, // General Repair
            'id_status' => 1, // Baru
            'id_prioritas' => 1, // Low (24 Jam)
            'id_departemen' => 1, // ENG
            'id_aset' => null,
            'foto_trouble' => null,
            'foto_bukti_selesai' => null,
            'tanggal_ajuan' => $wo3_date,
            'deadline' => $wo3_deadline,
            'assigned_to' => null,
            'verified_by' => null,
            'verified_at' => null,
            'reject_note' => null,
            'total_biaya_sparepart' => 0.00,
            'durasi_menit_pengerjaan' => null,
        ]);

        // WO 4: Escalated (Overdue SLA)
        $wo4_date = date('Y-m-d H:i:s', strtotime('-5 hours'));
        $wo4_deadline = date('Y-m-d H:i:s', strtotime('-1 hours'));
        $db->table('work_order')->insert([
            'id_wo' => 4,
            'kode_wo' => 'WO-20261018-0004',
            'judul_wo' => 'Elevator Lift 1 Tower A Mengeluarkan Suara Gesekan Kasar',
            'deskripsi' => 'Pintu lift bergetar dan sensor macet sesekali. Sangat darurat untuk segera diperbaiki teknisi sebelum jam checkout ramai.',
            'id_user' => 9, // Staf FO
            'id_kategori' => 1, // Maintenance
            'id_status' => 8, // Escalated
            'id_prioritas' => 3, // High (4 Jam)
            'id_departemen' => 1, // ENG
            'id_aset' => 2, // Elevator Lift 1
            'foto_trouble' => null,
            'foto_bukti_selesai' => null,
            'tanggal_ajuan' => $wo4_date,
            'deadline' => $wo4_deadline,
            'assigned_to' => 4, // Staf ENG
            'verified_by' => 3, // Kadep ENG
            'verified_at' => $wo4_date,
            'reject_note' => null,
            'total_biaya_sparepart' => 0.00,
            'durasi_menit_pengerjaan' => null,
        ]);

        // WO 5: Ditolak
        $wo5_date = date('Y-m-d H:i:s', strtotime('-1 day'));
        $db->table('work_order')->insert([
            'id_wo' => 5,
            'kode_wo' => 'WO-20261017-0005',
            'judul_wo' => 'Permintaan Tambahan TV di Pos Security',
            'deskripsi' => 'Mengajukan pengadaan TV 32 inch baru untuk pos security gerbang belakang.',
            'id_user' => 4,
            'id_kategori' => 2,
            'id_status' => 3, // Ditolak
            'id_prioritas' => 1,
            'id_departemen' => 2, // IT
            'id_aset' => null,
            'foto_trouble' => null,
            'foto_bukti_selesai' => null,
            'tanggal_ajuan' => $wo5_date,
            'deadline' => null,
            'assigned_to' => null,
            'verified_by' => 5, // Kadep IT
            'verified_at' => date('Y-m-d H:i:s', strtotime('-23 hours')),
            'reject_note' => 'Pengadaan baru bukan ranah perbaikan Work Order. Harap ajukan form Capex/Opex pengadaan barang ke General Manager.',
            'total_biaya_sparepart' => 0.00,
            'durasi_menit_pengerjaan' => null,
        ]);

        // 10. LOG AUDIT
        $db->table('log_audit')->emptyTable();
        $db->table('log_audit')->insertBatch([
            [
                'id_user' => 1,
                'aktivitas' => 'Inisialisasi sistem Work Order & Manajemen Aset V2.0',
                'modul' => 'System',
                'ip_address' => '127.0.0.1',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'id_user' => 9,
                'aktivitas' => 'Mengajukan Work Order baru: WO-20261016-0001',
                'modul' => 'Work Order',
                'ip_address' => '127.0.0.1',
                'timestamp' => $wo1_date,
            ],
            [
                'id_user' => 3,
                'aktivitas' => 'Menyetujui dan menugaskan WO-20261016-0001 ke Joko Prasetyo',
                'modul' => 'Work Order',
                'ip_address' => '127.0.0.1',
                'timestamp' => $wo1_verified,
            ],
            [
                'id_user' => 4,
                'aktivitas' => 'Menyelesaikan WO-20261016-0001 dan memotong inventaris sparepart',
                'modul' => 'Work Order',
                'ip_address' => '127.0.0.1',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days + 2 hours')),
            ],
        ]);
    }
}
