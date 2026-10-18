<?php

return [
    'title_list'            => 'Manajemen Aset Hotel',
    'subtitle_list'         => 'Inventarisasi fasilitas, pelacakan riwayat kerusakan, dan pencetakan label QR Code.',
    'title_create'          => 'Tambah Aset Hotel Baru',
    'subtitle_create'       => 'Sistem otomatis men-generate file QR Code untuk aset baru ini.',
    'title_edit'            => 'Edit Aset: {0}',
    'title_detail'          => 'Rincian Aset: {0}',
    'title_print_qr'        => 'Cetak Label QR Code Aset',
    'btn_print_all_qr'      => 'Cetak Semua QR Code',
    'btn_add_asset'         => 'Tambah Aset Baru',
    
    // Kondisi Aset
    'cond_good'             => 'Baik',
    'cond_light_damage'     => 'Rusak Ringan',
    'cond_heavy_damage'     => 'Rusak Berat',
    
    // Kolom Tabel
    'col_qr'                => 'QR Code',
    'col_code'              => 'Kode Aset',
    'col_name'              => 'Nama Aset',
    'col_dept'              => 'Departemen',
    'col_location'          => 'Lokasi Spesifik',
    'col_condition'         => 'Kondisi',
    'col_actions'           => 'Aksi',
    
    // Form fields
    'field_code'            => 'Kode Aset Unik',
    'field_code_help'       => 'Gunakan format standar per departemen.',
    'field_code_locked'     => 'Kode aset tidak dapat diubah setelah QR Code digenerate.',
    'field_name'            => 'Nama Aset / Peralatan',
    'field_dept'            => 'Departemen Penanggung Jawab',
    'field_select_dept'     => '-- Pilih Departemen --',
    'field_condition'       => 'Kondisi Aset Saat Ini',
    'field_location'        => 'Lokasi Penempatan Spesifik',
    'btn_save_asset'        => 'Simpan & Generate QR Code',
    'btn_save_changes'      => 'Simpan Perubahan',
    
    // Detail Page
    'report_damage_this'    => 'Laporkan Kerusakan Aset Ini',
    'print_qr_btn'          => 'Cetak QR',
    'qr_label_title'        => 'Label QR Code Aset',
    'download_qr_image'     => 'Unduh File Gambar QR',
    'facility_specs'        => 'Spesifikasi Fasilitas',
    'physical_condition'    => 'Kondisi Fisik:',
    'physical_location'     => 'Lokasi Fisik:',
    'registration_date'     => 'Tanggal Registrasi:',
    'total_incidents'       => 'Total Insiden:',
    'incident_count'        => '{0} Kali Perbaikan',
    'repair_history_title'  => 'Riwayat Perbaikan & Maintenance',
    'repair_history_sub'    => 'Seluruh tiket Work Order yang tercatat pernah menangani aset ini.',
    'report_new_damage'     => 'Lapor Kerusakan Baru',
    'prime_condition'       => 'Kondisi Aset Prima',
    'no_damage_recorded'    => 'Belum ada riwayat perbaikan atau laporan kerusakan untuk aset ini.',
    
    // Scan Page
    'scan_title'            => 'Scanner QR Code Aset',
    'scan_subtitle'         => 'Arahkan kamera ke label QR Code fisik aset atau pilih dari simulasi cepat di bawah.',
    'open_camera'           => 'Buka Kamera',
    'close_camera'          => 'Matikan Kamera',
    'quick_simulation'      => 'Simulasi Scan Cepat (Untuk Pengujian & Demo):',
    'asset_detected_title'  => 'Aset Berhasil Terdeteksi!',
    'managing_dept'         => 'Departemen Pengelola:',
    'current_condition'     => 'Kondisi Aset Terkini:',
    'btn_report_locked'     => 'Laporkan Kerusakan Aset Ini (Kunci Otomatis Form WO)',
    'btn_view_history'      => 'Lihat Kondisi & Riwayat Perbaikan',
    'print_sheet_title'     => 'Lembar Cetak Label QR Code Aset Hotel Golden Hill Batu',
    'print_sheet_ready'     => 'Total {0} Aset Siap Dicetak',
    'print_sheet_btn'       => 'Cetak Label Sekarang',
    
    // Camera & Fallback Mitigation
    'torch_toggle'          => 'Lampu Senter',
    'switch_camera'         => 'Ganti Kamera',
    'camera_error_denied'   => 'Akses kamera tidak diizinkan atau tidak terdeteksi. Silakan aktifkan izin kamera atau gunakan opsi alternatif di bawah.',
    'camera_error_dark'     => 'Pencahayaan redup? Gunakan tombol senter atau masukkan kode aset secara langsung.',
    'fallback_manual_title' => 'Alur Cadangan / Alternatif Scanner Kamera',
    'fallback_manual_input' => 'Input Kode Aset Manual',
    'fallback_dropdown_select' => 'Pilih Aset dari Daftar Hotel',
    'fallback_select_placeholder' => '-- Pilih Aset dari Daftar (Pencarian Cepat) --',
    'btn_process_code'      => 'Cari & Proses Aset',
];
