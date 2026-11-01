<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h5 class="fw-bold text-dark mb-1"><?= lang('Auth.login_title') ?></h5>
<p class="text-muted small mb-4"><?= lang('Auth.login_subtitle') ?></p>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger py-2 px-3 small border-0 mb-3">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 px-3 small border-0 mb-3">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('login') ?>" method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="username" class="form-label small fw-semibold text-secondary"><?= lang('Auth.username_or_email') ?></label>
        <input type="text" class="form-control" id="username" name="username" placeholder="<?= lang('Auth.username_placeholder') ?>" required value="<?= old('username') ?>">
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label for="password" class="form-label small fw-semibold text-secondary"><?= lang('Auth.password') ?></label>
            <a href="<?= base_url('forgot-password') ?>" class="small text-decoration-none text-muted"><?= lang('Auth.forgot_password_link') ?></a>
        </div>
        <input type="password" class="form-control" id="password" name="password" placeholder="<?= lang('Auth.password_placeholder') ?>" required>
    </div>

    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-gold">
            <?= lang('Auth.btn_login') ?>
        </button>
    </div>
</form>

<div class="card bg-light border-0 p-3 rounded-3 mt-3">
    <div class="small fw-bold text-dark mb-2"><?= lang('Auth.quick_login_title') ?></div>
    <div class="d-flex flex-wrap gap-1">
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('admin', 'admin123')">
            Admin
        </button>
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('kadep_eng', 'kadep123')">
            Kadep ENG
        </button>
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('staf_eng', 'staf123')">
            Staf ENG
        </button>
        <button type="button" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;" onclick="fillLogin('staf_hk', 'staf123')">
            Staf HK
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
