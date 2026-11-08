<!DOCTYPE html>
<html lang="<?= current_locale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? lang('Auth.login_title')) ?> - <?= lang('Navigation.hotel_name') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <!-- Language Switcher in Auth Header -->
            <?php $currentLocale = current_locale(); ?>
            <div class="lang-switch-auth">
                <div class="btn-group btn-group-sm">
                    <a href="<?= base_url('lang/id') ?>" class="btn btn-outline-light py-0 px-2 <?= $currentLocale === 'id' ? 'active bg-warning text-dark border-warning fw-bold' : 'text-white-50' ?>" style="font-size: 0.72rem;">
                        🇮🇩 ID
                    </a>
                    <a href="<?= base_url('lang/en') ?>" class="btn btn-outline-light py-0 px-2 <?= $currentLocale === 'en' ? 'active bg-warning text-dark border-warning fw-bold' : 'text-white-50' ?>" style="font-size: 0.72rem;">
                        🇬🇧 EN
                    </a>
                </div>
            </div>

            <span class="hotel-badge"><?= lang('Navigation.hotel_name') ?></span>
            <h4 class="fw-bold mb-1"><?= lang('Navigation.app_name') ?></h4>
            <p class="text-white-50 small mb-0"><?= lang('Navigation.hospitality') ?></p>
        </div>
        <div class="p-4 p-md-5">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
