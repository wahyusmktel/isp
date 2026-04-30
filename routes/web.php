<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// ─── Guest (belum login) ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register.post');
});

// ─── Customer Portal ─────────────────────────────────────────────────────────
Route::get('/customer/login', [\App\Http\Controllers\CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/customer/login', [\App\Http\Controllers\CustomerAuthController::class, 'login'])->name('customer.login.post');
Route::post('/customer/logout', [\App\Http\Controllers\CustomerAuthController::class, 'logout'])->name('customer.logout');
Route::get('/customer/dashboard', [\App\Http\Controllers\CustomerPortalController::class, 'dashboard'])->name('customer.dashboard');

// ─── Authenticated (harus login) ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    Route::get('/packages', [\App\Http\Controllers\PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages', [\App\Http\Controllers\PackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{package}', [\App\Http\Controllers\PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [\App\Http\Controllers\PackageController::class, 'destroy'])->name('packages.destroy');
    Route::patch('/packages/{package}/toggle', [\App\Http\Controllers\PackageController::class, 'toggleStatus'])->name('packages.toggle');
    Route::post('/packages/generate-dummy', [\App\Http\Controllers\PackageController::class, 'generateDummy'])->name('packages.generate_dummy');

    Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/search', [\App\Http\Controllers\CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/activities', [\App\Http\Controllers\CustomerActivityController::class, 'index'])->name('customers.activities');
    Route::get('/customers/activities/latest', [\App\Http\Controllers\CustomerActivityController::class, 'latestApi'])->name('customers.activities.latest');
    Route::post('/customers/activities/webhook', [\App\Http\Controllers\CustomerActivityController::class, 'webhook'])->name('customers.activities.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/live-traffic', [\App\Http\Controllers\CustomerController::class, 'liveTraffic'])->name('customers.live_traffic');
    Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::patch('/customers/{customer}/status', [\App\Http\Controllers\CustomerController::class, 'updateStatus'])->name('customers.status');
    Route::post('/customers/generate-dummy', [\App\Http\Controllers\CustomerController::class, 'generateDummy'])->name('customers.generate_dummy');

    Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/generate-mass', [\App\Http\Controllers\InvoiceController::class, 'generateMass'])->name('invoices.generate_mass');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class, 'printPdf'])->name('invoices.pdf');
    Route::post('/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::patch('/invoices/{invoice}/status', [\App\Http\Controllers\InvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::patch('/invoices/{invoice}/payment-method', [\App\Http\Controllers\InvoiceController::class, 'updatePaymentMethod'])->name('invoices.payment_method');

    Route::get('/network', [\App\Http\Controllers\RouterController::class, 'index'])->name('network.index');
    Route::post('/routers', [\App\Http\Controllers\RouterController::class, 'store'])->name('routers.store');
    Route::put('/routers/{router}', [\App\Http\Controllers\RouterController::class, 'update'])->name('routers.update');
    Route::delete('/routers/{router}', [\App\Http\Controllers\RouterController::class, 'destroy'])->name('routers.destroy');
    Route::post('/routers/{router}/test', [\App\Http\Controllers\RouterController::class, 'testConnection'])->name('routers.test');
    Route::get('/routers/{router}/live', [\App\Http\Controllers\RouterController::class, 'liveData'])->name('routers.live');

    Route::post('/odps', [\App\Http\Controllers\OdpController::class, 'store'])->name('odps.store');
    Route::put('/odps/{odp}', [\App\Http\Controllers\OdpController::class, 'update'])->name('odps.update');
    Route::delete('/odps/{odp}', [\App\Http\Controllers\OdpController::class, 'destroy'])->name('odps.destroy');

    Route::get('/pppoe-mapping', [\App\Http\Controllers\PppoeMappingController::class, 'index'])->name('pppoe-mapping.index');
    Route::get('/pppoe-mapping/{router}/secrets', [\App\Http\Controllers\PppoeMappingController::class, 'fetchSecrets'])->name('pppoe-mapping.secrets');
    Route::post('/pppoe-mapping/map', [\App\Http\Controllers\PppoeMappingController::class, 'mapCustomer'])->name('pppoe-mapping.map');
    Route::delete('/pppoe-mapping/unmap/{customer}', [\App\Http\Controllers\PppoeMappingController::class, 'unmapCustomer'])->name('pppoe-mapping.unmap');
    Route::post('/pppoe-mapping/auto-map', [\App\Http\Controllers\PppoeMappingController::class, 'autoMap'])->name('pppoe-mapping.auto-map');

    Route::get('/monitoring', [\App\Http\Controllers\MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/refresh', [\App\Http\Controllers\MonitoringController::class, 'refresh'])->name('monitoring.refresh');

    Route::get('/traffic', [\App\Http\Controllers\TrafficMonitorController::class, 'index'])->name('traffic.index');
    Route::get('/traffic/{router}', [\App\Http\Controllers\TrafficMonitorController::class, 'fetchTraffic'])->name('traffic.fetch');

    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    Route::get('/financial', [\App\Http\Controllers\FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/expenses', [\App\Http\Controllers\FinancialController::class, 'store'])->name('financial.expenses.store');
    Route::put('/financial/expenses/{expense}', [\App\Http\Controllers\FinancialController::class, 'update'])->name('financial.expenses.update');
    Route::delete('/financial/expenses/{expense}', [\App\Http\Controllers\FinancialController::class, 'destroy'])->name('financial.expenses.destroy');
});
