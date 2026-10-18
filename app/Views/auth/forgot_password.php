<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h5 class="fw-bold text-dark mb-1"><?= lang('Auth.forgot_title') ?></h5>
<p class="text-muted small mb-4"><?= lang('Auth.forgot_subtitle') ?></p>

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

<?php if (session()->getFlashdata('reset_link_simulation')): ?>
    <div class="card bg-warning bg-opacity-10 border border-warning p-3 rounded-3 mb-3">
        <div class="small fw-bold text-dark mb-1"><i class="bi bi-whatsapp text-success me-1"></i> <?= lang('Auth.reset_simulation_msg') ?></div>
        <div class="small text-break mb-2"><?= current_locale() === 'en' ? 'Password reset link sent via WhatsApp / Email. Click below to test:' : 'Tautan reset password terkirim via WhatsApp / Email. Klik langsung untuk menguji:' ?></div>
        <a href="<?= session()->getFlashdata('reset_link_simulation') ?>" class="btn btn-sm btn-dark text-break" style="font-size: 0.75rem;">
            <?= session()->getFlashdata('reset_link_simulation') ?>
        </a>
    </div>
<?php endif; ?>

<form action="<?= base_url('forgot-password') ?>" method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold text-secondary"><?= lang('Auth.registered_email') ?></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
            <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="nama@goldenhill.com" required value="<?= old('email') ?>">
        </div>
        <div class="form-text small"><?= current_locale() === 'en' ? 'Registered email example: ' : 'Contoh email terdaftar: ' ?><code>admin@goldenhill.com</code></div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-gold">
            <i class="bi bi-send me-1"></i> <?= lang('Auth.btn_send_reset') ?>
        </button>
    </div>

    <div class="text-center">
        <a href="<?= base_url('login') ?>" class="small text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> <?= lang('Auth.back_to_login') ?>
        </a>
    </div>
</form>
<?= $this->endSection() ?>
