<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center py-4">
    <div class="col-md-7 text-center">
        <div class="card card-custom p-5 border-0 shadow-sm">
            <div class="mb-4">
                <span class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 90px; height: 90px;">
                    <i class="bi bi-wifi-off fs-1"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-2">Mode Offline Aktif</h3>
            <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">
                Koneksi internet terputus atau sinyal tidak terjangkau (area ruang mesin/basement). 
                Sistem PWA Hotel Golden Hill tetap aktif dan data yang Anda masukkan akan disimpan secara lokal di perangkat ini.
            </p>

            <div class="card bg-light border-0 p-3 mb-4 rounded-3 text-start">
                <div class="small fw-bold text-dark mb-2"><i class="bi bi-shield-check text-primary me-2"></i>Fitur yang Tetap Berfungsi Offline:</div>
                <ul class="small text-muted mb-0 ps-3">
                    <li>Pengisian formulir Work Order baru (tersimpan otomatis sebagai draft).</li>
                    <li>Pemindaian kode aset fisik secara lokal.</li>
                    <li>Otomatis sinkronisasi ke server pusat saat perangkat kembali online.</li>
                </ul>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="<?= base_url('workorder/create') ?>" class="btn btn-gold btn-mobile-touch px-4" style="min-height: 48px;">
                    <i class="bi bi-plus-circle me-1"></i> Buat Draft WO Baru
                </a>
                <button type="button" class="btn btn-outline-dark btn-mobile-touch px-4" style="min-height: 48px;" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Periksa Koneksi Ulang
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
