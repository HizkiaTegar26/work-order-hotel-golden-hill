<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Root Route
$routes->get('/', function() {
    if (session()->get('isLoggedIn')) {
        return redirect()->to(base_url('dashboard'));
    }
    return redirect()->to(base_url('login'));
});

// Authentication Routes (Public)
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->match(['get', 'post'], 'forgot-password', 'Auth::forgotPassword');
$routes->match(['get', 'post'], 'reset-password/(:segment)', 'Auth::resetPassword/$1');
$routes->post('auth/switch-role', 'Auth::switchRole');
$routes->get('lang/(:segment)', 'Language::switchLocale/$1');
$routes->get('offline', 'WorkOrder::offlineView');

// Protected Routes (Require Login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Work Order Module
    $routes->get('workorder', 'WorkOrder::index');
    $routes->get('workorder/create', 'WorkOrder::create');
    $routes->post('workorder/store', 'WorkOrder::store');
    $routes->post('workorder/sync-offline-draft', 'WorkOrder::syncOfflineDraft');
    $routes->get('workorder/detail/(:num)', 'WorkOrder::detail/$1');
    $routes->get('workorder/edit/(:num)', 'WorkOrder::edit/$1');
    $routes->post('workorder/update/(:num)', 'WorkOrder::update/$1');
    $routes->get('workorder/delete/(:num)', 'WorkOrder::delete/$1');
    $routes->post('workorder/verify/(:num)', 'WorkOrder::verify/$1');
    $routes->post('workorder/assign/(:num)', 'WorkOrder::assign/$1');
    $routes->post('workorder/change-priority/(:num)', 'WorkOrder::changePriority/$1');
    $routes->post('workorder/start-process/(:num)', 'WorkOrder::startProcess/$1');
    $routes->post('workorder/finish/(:num)', 'WorkOrder::finish/$1');
    $routes->get('workorder/export-excel', 'WorkOrder::exportExcel');
    $routes->get('workorder/export-pdf/(:num)', 'WorkOrder::exportPdf/$1');
    $routes->get('workorder/monitoring', 'WorkOrder::monitoring', ['filter' => 'role:admin,kadep']);

    // QR Code Scanner Module
    $routes->get('scan', 'Scan::index');
    $routes->get('scan/lookup', 'Scan::lookup');

    // Asset Management Module
    $routes->get('aset', 'Aset::index');
    $routes->get('aset/create', 'Aset::create');
    $routes->post('aset/store', 'Aset::store');
    $routes->get('aset/detail/(:num)', 'Aset::detail/$1');
    $routes->get('aset/edit/(:num)', 'Aset::edit/$1');
    $routes->post('aset/update/(:num)', 'Aset::update/$1');
    $routes->get('aset/delete/(:num)', 'Aset::delete/$1', ['filter' => 'role:admin']);
    $routes->get('aset/print-qr', 'Aset::printQr');
    $routes->get('aset/print-qr/(:num)', 'Aset::printQr/$1');

    // Inventaris / Suku Cadang Module
    $routes->get('inventaris', 'Inventaris::index');
    $routes->post('inventaris/store', 'Inventaris::store');
    $routes->post('inventaris/update/(:num)', 'Inventaris::update/$1');
    $routes->get('inventaris/delete/(:num)', 'Inventaris::delete/$1', ['filter' => 'role:admin']);

    // Admin Only Modules
    $routes->group('', ['filter' => 'role:admin'], function($routes) {
        // Users Management
        $routes->get('users', 'User::index');
        $routes->post('users/store', 'User::store');
        $routes->post('users/update/(:num)', 'User::update/$1');
        $routes->get('users/toggle/(:num)', 'User::toggleStatus/$1');
        $routes->get('users/delete/(:num)', 'User::delete/$1');

        // Department Management
        $routes->get('departemen', 'Departemen::index');
        $routes->post('departemen/store', 'Departemen::store');
        $routes->post('departemen/update/(:num)', 'Departemen::update/$1');
        $routes->get('departemen/delete/(:num)', 'Departemen::delete/$1');

        // Audit Trail Logs
        $routes->get('audit', 'Audit::index');
    });
});
