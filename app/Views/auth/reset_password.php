<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h5 class="fw-bold text-dark mb-1"><?= lang('Auth.reset_title') ?></h5>
<p class="text-muted small mb-4"><?= lang('Auth.reset_subtitle', [esc($email)]) ?></p>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger py-2 px-3 small border-0 mb-3">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('reset-password/' . esc($token)) ?>" method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold text-secondary"><?= lang('Auth.new_password') ?></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
            <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="<?= lang('Auth.pass_min_length') ?>" required>
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirm" class="form-label small fw-semibold text-secondary"><?= lang('Auth.confirm_new_password') ?></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
            <input type="password" class="form-control border-start-0" id="password_confirm" name="password_confirm" placeholder="<?= lang('Auth.confirm_new_password') ?>" required>
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-gold">
            <i class="bi bi-check-lg me-1"></i> <?= lang('Auth.btn_save_password') ?>
        </button>
    </div>

    <div class="text-center">
        <a href="<?= base_url('login') ?>" class="small text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> <?= lang('Auth.back_to_login') ?>
        </a>
    </div>
</form>
<?= $this->endSection() ?>
