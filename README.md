# SISTEM INFORMASI WORK ORDER, MANAJEMEN ASET, DAN ANALYTIC PERFORMANCE V2.0
### Hotel Golden Hill by Golden Tulip Batu
**Berdasarkan Tugas Akhir:** Hizkia Tegar Kurniawan (NIM: 223140707111136, Universitas Brawijaya)  
**Tingkat Pengembangan:** Enterprise Industrial Grade (V2.0)

---

## 1. Arsitektur & Teknologi Sistem
- **Backend Framework:** PHP 8.2+ dengan CodeIgniter 4 (MVC Architecture)
- **Basis Data:** MySQL / MariaDB (Database: `ci4_workorder_v2`)
- **Frontend UI/UX:** Bootstrap 5, HTML5, Custom CSS3, Vanilla JS, DataTables.net, Chart.js 4.4
- **Pustaka Pendukung Enterprise:**
  - **Excel Engine:** `phpoffice/phpspreadsheet`
  - **PDF Generator:** `dompdf/dompdf`
  - **QR Code Engine:** `endroid/qr-code` (Backend PNG Generator) & `html5-qrcode` (Live Camera Scanner)
  - **WhatsApp API Gateway:** Fonnte / Wablas integration module with local simulation sandbox fallback
- **Server Environment:** XAMPP / Apache Local Architecture (`http://localhost/work-order/`)

---

## 2. Struktur Database Relasional (12 Tabel)
Database `ci4_workorder_v2` dirancang dengan relasi InnoDB, Primary Key, Foreign Key Constraints, dan Indexing:
1. `roles`: Master peran pengguna (`Admin`, `Kepala Departemen`, `Staf`).
2. `departemen`: Master unit operasional (`Engineering & Maintenance`, `IT & Systems`, `Housekeeping`, `Front Office`, `Food & Beverage`).
3. `users`: Entitas pengguna dengan hash password BCRYPT, nomor WhatsApp, relasi departemen, dan peran.
4. `kategori_wo`: Klasifikasi tiket (`Maintenance`, `IT Support`, `Housekeeping Service`, `General Repair`).
5. `status_wo`: Alur siklus tiket (`Baru`, `Accepted`, `Ditolak`, `Ditugaskan`, `Proses`, `Selesai`, `Menunggu Konfirmasi`, `Escalated`).
6. `prioritas_sla`: Target penyelesaian (`Low` 24 Jam, `Medium` 12 Jam, `High` 4 Jam, `Emergency` 1 Jam).
7. `aset_hotel`: Master fasilitas hotel, kode aset unik, departemen, lokasi fisik, path gambar QR code, dan status kondisi fisik (`Baik`, `Rusak Ringan`, `Rusak Berat`).
8. `inventaris_barang`: Stok suku cadang hotel, satuan, dan harga satuan material.
9. `work_order`: Master transaksi tiket perbaikan, relasi pelapor, departemen penanggung jawab, teknisi handler, approval kadep, durasi nyata pengerjaan, dan kalkulasi biaya.
10. `wo_spareparts`: Relasi many-to-many pemakaian suku cadang per work order beserta subtotal biaya.
11. `log_audit`: Rekam jejak seluruh transaksi CRUD, autentikasi login/logout, eskalasi otomatis, dan IP address.
12. `reset_password_token`: Token terenkripsi self-service pemulihan kata sandi.

---

## 3. Akun Pengguna Default (Multi-Role)
| No | Role | Username | Password | Nama Lengkap | Departemen |
|:---|:---|:---|:---|:---|:---|
| 1 | **Admin** | `admin` | `admin123` | Administrator Hotel | IT & Systems |
| 2 | **Admin** | `hizkia` | `hizkia123` | Hizkia Tegar Kurniawan | IT & Systems |
| 3 | **Kepala Departemen** | `kadep_eng` | `kadep123` | Ir. Budi Santoso | Engineering & Maintenance |
| 4 | **Staf** | `staf_eng` | `staf123` | Joko Prasetyo | Engineering & Maintenance |
| 5 | **Kepala Departemen** | `kadep_it` | `kadep123` | Rian Pratama, S.Kom | IT & Systems |
| 6 | **Staf** | `staf_it` | `staf123` | Fajar Ramadhan | IT & Systems |
| 7 | **Kepala Departemen** | `kadep_hk` | `kadep123` | Siti Aminah | Housekeeping |
| 8 | **Staf** | `staf_hk` | `staf123` | Dewi Lestari | Housekeeping |
| 9 | **Staf** | `staf_fo` | `staf123` | Reza Firmansyah | Front Office |

*Fitur Quick Role Switcher*: Penguji dapat beralih antar role akun secara instan melalui tombol **Switch Role (Demo)** pada sidebar bawah untuk memvalidasi alur tanpa perlu bolak-balik logout.

---

## 4. 6 Fitur Unggulan & Advanced Modules
1. **Auto Escalation & Live SLA Countdown Timer:**
   - Menghitung sisa waktu penyelesaian otomatis berdasarkan prioritas SLA.
   - Indikator visual real-time (Badge Hijau: Aman, Kuning: <30% tersisa, Merah/Blinking: Overdue).
   - Mesin eskalasi otomatis mengubah status tiket menjadi `Escalated` saat melewati batas target deadline dan memicu peringatan darurat ke manajemen.
2. **Integrasi WhatsApp Gateway API:**
   - Mengirim notifikasi otomatis saat WO Baru diajukan, WO Ditugaskan ke teknisi, WO Ditolak dengan alasan spesifik, serta peringatan darurat saat status Escalated.
   - Terintegrasi dengan Fonnte / Wablas API Gateway serta mode simulasi sandbox log lokal (`writable/logs/whatsapp_gateway.log`).
3. **QR Code Scanner Aset Hotel:**
   - Pemindai kamera HTML5 langsung dari web browser.
   - Menampilkan kondisi fisik dan riwayat seluruh perbaikan aset terkait.
   - Tombol pintas *"Laporkan Kerusakan Aset Ini"* yang otomatis mengunci kode aset dan departemen pada form tiket WO.
4. **Pemotongan Inventaris & Biaya Maintenance:**
   - Saat staf menyelesaikan WO, teknisi dapat memilih material suku cadang yang digunakan.
   - Sistem secara atomik memotong stok gudang (aman dari konkurensi) dan menghitung total pengeluaran untuk akuntansi hotel.
5. **Executive Analytics & KPI Dashboard:**
   - Grafik interaktif Chart.js:
     - *Average Response Time* (Ajuan ke Verifikasi)
     - *Average Resolution Time* (Penugasan ke Selesai)
     - *SLA Compliance Rate* (% Tuntas Tepat Waktu vs Overdue)
     - *Distribusi Biaya Sparepart per Departemen*
     - *Top 5 Aset Hotel Paling Sering Rusak*
6. **Keamanan, Reset Password & Audit Trail:**
   - Fitur lupa kata sandi dengan token terenkripsi.
   - Pencatatan aktivitas audit trail lengkap dengan IP address pengguna pada tabel `log_audit`.

---

## 5. Validasi & Pengujian Black Box (25 Test Cases)
Untuk menjalankan suite pengujian otomatis 25 test cases:
```bash
php spark test:blackbox
```
atau
```bash
php tests/BlackBoxValidationTest.php
```

Hasil pengujian: **25 VALID (PASS) / 0 GAGAL (100% KELULUSAN)**.

---

## 6. CLI Commands
- `php spark wo:escalate` : Menjalankan evaluasi manual auto escalation tiket overdue.
- `php spark test:blackbox` : Menjalankan pengujian 25 skenario validasi Black Box.
- `php spark db:seed DatabaseSeeder` : Mereset & mengisi ulang data seeder realistis.
