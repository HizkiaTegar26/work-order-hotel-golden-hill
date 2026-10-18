<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h5 class="fw-bold text-dark mb-1"><?= lang('Auth.login_title') ?></h5>
<p class="text-muted small mb-4"><?= lang('Auth.login_subtitle') ?></p>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger py-2 px-3 small border-0 mb-3">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 px-3 small border-0 mb-3">
        <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('login') ?>" method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="username" class="form-label small fw-semibold text-secondary"><?= lang('Auth.username_or_email') ?></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
            <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="<?= lang('Auth.username_placeholder') ?>" required value="<?= old('username') ?>">
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label for="password" class="form-label small fw-semibold text-secondary"><?= lang('Auth.password') ?></label>
            <a href="<?= base_url('forgot-password') ?>" class="small text-decoration-none text-muted"><?= lang('Auth.forgot_password_link') ?></a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
            <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="<?= lang('Auth.password_placeholder') ?>" required>
        </div>
    </div>

    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-gold">
            <i class="bi bi-box-arrow-in-right me-1"></i> <?= lang('Auth.btn_login') ?>
        </button>
    </div>
</form>

<div class="card bg-light border-0 p-3 rounded-3 mt-3">
    <div class="small fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-1"></i> <?= lang('Auth.quick_login_title') ?></div>
    <div class="d-flex flex-wrap gap-1">
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('admin', 'admin123')">
            <i class="bi bi-shield-lock me-1"></i> Admin
        </button>
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('kadep_eng', 'kadep123')">
            <i class="bi bi-person-badge me-1"></i> Kadep ENG
        </button>
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('staf_eng', 'staf123')">
            <i class="bi bi-tools me-1"></i> Staf ENG
        </button>
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('staf_hk', 'staf123')">
            <i class="bi bi-brush me-1"></i> Staf HK
        </button>
    </div>
</div>

<script>
    function fillLogin(user, pass) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
    }
</script>
<?= $this->endSection() ?>
