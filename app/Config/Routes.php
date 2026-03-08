<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Auth Routes
$routes->get('/', 'Home::index'); // Landing Page
$routes->get('/pricing', 'Subscription::pricing'); // Pricing Page
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::process_login');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::process_register');
$routes->get('/logout', 'Auth::logout');

// Dashboard Routes (Protected)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    
    // Clients Routes
    $routes->group('clients', function($routes) {
        $routes->get('/', 'Clients::index');
        $routes->get('create', 'Clients::create');
        $routes->post('store', 'Clients::store');
        $routes->post('store_ajax', 'Clients::store_ajax');
        $routes->get('show/(:num)', 'Clients::show/$1');
        $routes->get('edit/(:num)', 'Clients::edit/$1');
        $routes->post('update/(:num)', 'Clients::update/$1');
        $routes->post('delete/(:num)', 'Clients::delete/$1');
    });

    // Invoices Routes
    $routes->group('invoices', function($routes) {
        $routes->get('/', 'Invoices::index');
        $routes->get('export', 'Invoices::export');
        $routes->get('create', 'Invoices::create');
        $routes->post('store', 'Invoices::store');
        $routes->get('edit/(:segment)', 'Invoices::edit/$1');
        $routes->post('update/(:segment)', 'Invoices::update/$1');
        $routes->get('show/(:segment)', 'Invoices::show/$1');
        $routes->get('pdf/(:segment)', 'Invoices::downloadPdf/$1');
        $routes->get('zip/(:segment)', 'Invoices::downloadZip/$1');
        $routes->get('toggle-reminder/(:segment)', 'Invoices::toggleReminder/$1');
        $routes->post('status/(:segment)', 'Invoices::updateStatus/$1');
        $routes->post('delete/(:segment)', 'Invoices::delete/$1');
    });
    // Reports Routes
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Reports::index');
        $routes->get('clients', 'Reports::clients');
    });

    // Settings
    // Profil Perusahaan
    $routes->get('settings/profile', 'Settings::profile');
    $routes->post('settings/profile/update', 'Settings::updateProfile');
    $routes->post('settings/profile/remove-logo', 'Settings::removeLogo');
    $routes->post('settings/profile/remove-signature', 'Settings::removeSignature');
    $routes->post('settings/profile/remove-qr', 'Settings::removeQr');



    // SaaS Admin Route Group
    $routes->group('admin', ['filter' => 'admin_filter'], function($routes){
        $routes->get('/', 'AdminDashboard::index');
        
        // Admin Profil
        $routes->get('profile', 'AdminProfile::index');
        $routes->post('profile/update', 'AdminProfile::update');
        
        // Manajemen User
        $routes->get('users', 'AdminUser::index');
        $routes->post('users/toggle/(:num)', 'AdminUser::toggleStatus/$1');
        
        // Langganan
        $routes->get('subscriptions', 'AdminSubscription::index');
        $routes->get('subscriptions/export', 'AdminSubscription::export');
        $routes->post('subscriptions/bulk-action', 'AdminSubscription::bulkAction');
        $routes->post('approve', 'AdminSubscription::approve');
        $routes->post('reject', 'AdminSubscription::reject');
        $routes->get('view-proof/(:segment)', 'AdminSubscription::viewProof/$1');
        
        // Pembayaran
        $routes->get('payments', 'AdminPayment::index');
        
        // Paket & Pricing
        $routes->get('plans', 'AdminPlan::index');
        $routes->post('plans/update', 'AdminPlan::update');
        
        // Kupon Diskon
        $routes->get('coupons', 'AdminCoupon::index');
        $routes->post('coupons/store', 'AdminCoupon::store');
        $routes->post('coupons/toggle/(:num)', 'AdminCoupon::toggle/$1');
        $routes->post('coupons/delete/(:num)', 'AdminCoupon::delete/$1');
        
        // Laporan SaaS
        $routes->get('reports', 'AdminReport::index');
        
        // Sistem
        $routes->get('system', 'AdminSystem::index');
    });

    // Subscription Flow (Protected)
    $routes->post('/subscribe', 'Subscription::subscribe');
    $routes->post('/coupon/apply', 'Subscription::applyCoupon');
    $routes->get('/subscription/upload/(:num)', 'Subscription::uploadView/$1');
    $routes->post('/subscription/upload-proof', 'Subscription::uploadProof');
});

// Route untuk Stress Test Dompdf (DEVELOPMENT ONLY)
$routes->cli('stresstest', 'StressTest::index');
$routes->get('stresstest', 'StressTest::index');

// Public Routes
$routes->get('/v/zip/(:segment)', 'PublicInvoice::downloadZip/$1');
$routes->get('/v/(:segment)', 'PublicInvoice::index/$1'); // UUID (Public View)
