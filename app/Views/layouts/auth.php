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
    <style>
        :root {
            --primary-navy: #0f172a;
            --secondary-navy: #1e293b;
            --accent-gold: #d97706;
            --accent-gold-light: #fbbf24;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            padding: 1.5rem;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 470px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .auth-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 2rem 2rem 1.75rem;
            text-align: center;
            position: relative;
        }
        .auth-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d97706, #fbbf24, #d97706);
        }
        .hotel-badge {
            display: inline-block;
            background: rgba(217, 119, 6, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 50px;
            padding: 0.25rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .btn-gold {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
            transform: translateY(-1px);
        }
        .form-control {
            border-radius: 0.75rem;
            padding: 0.7rem 1rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.15);
        }
        .lang-switch-auth {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
    </style>
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

            <span class="hotel-badge"><i class="bi bi-building me-1"></i> <?= lang('Navigation.hotel_name') ?></span>
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
