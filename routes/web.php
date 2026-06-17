<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerActivityController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\CustomerTicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\IsolationController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\NewsAdminController;
use App\Http\Controllers\NewsCommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OdpController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PppoeMappingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TrafficMonitorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/', function () {
    return view('welcome');
});

// ─── Public News ─────────────────────────────────────────────────────────────
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::post('/berita/{news}/komentar', [NewsCommentController::class, 'store'])->name('news.comment.store');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');
Route::get('/isolir-portal', [IsolationController::class, 'portal'])->name('isolation.portal');

// ─── Guest (belum login) ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// ─── Customer Portal ─────────────────────────────────────────────────────────
Route::get('/customer/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
Route::get('/customer/dashboard', [CustomerPortalController::class, 'dashboard'])->name('customer.dashboard');
Route::post('/customer/tickets', [CustomerTicketController::class, 'store'])->name('customer.tickets.store');

Route::post('/customers/activities/webhook', [CustomerActivityController::class, 'webhook'])
    ->name('customers.activities.webhook')
    ->withoutMiddleware([PreventRequestsDuringMaintenance::class])
    ->withoutMiddleware([PreventRequestForgery::class]);

// ─── Authenticated (harus login) ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::get('/whatsapp/status', [WhatsAppController::class, 'status'])->name('whatsapp.status');
    Route::get('/whatsapp/groups', [WhatsAppController::class, 'groups'])->name('whatsapp.groups');
    Route::post('/whatsapp/connect', [WhatsAppController::class, 'connect'])->name('whatsapp.connect');
    Route::post('/whatsapp/logout', [WhatsAppController::class, 'logout'])->name('whatsapp.logout');
    Route::post('/whatsapp/reset', [WhatsAppController::class, 'reset'])->name('whatsapp.reset');
    Route::post('/whatsapp/settings', [WhatsAppController::class, 'saveSettings'])->name('whatsapp.settings');
    Route::post('/whatsapp/test-message', [WhatsAppController::class, 'sendTest'])->name('whatsapp.test-message');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
    Route::patch('/packages/{package}/toggle', [PackageController::class, 'toggleStatus'])->name('packages.toggle');
    Route::post('/packages/generate-dummy', [PackageController::class, 'generateDummy'])->name('packages.generate_dummy');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/activities', [CustomerActivityController::class, 'index'])->name('customers.activities');
    Route::get('/customers/activities/latest', [CustomerActivityController::class, 'latestApi'])->name('customers.activities.latest');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/live-traffic', [CustomerController::class, 'liveTraffic'])->name('customers.live_traffic');
    Route::any('/customers/{customer}/ont-admin-proxy/{path?}', [CustomerController::class, 'ontAdminProxy'])
        ->where('path', '.*')
        ->name('customers.ont_admin_proxy');
    Route::get('/customers/{customer}/ont-info', [CustomerController::class, 'ontInfo'])->name('customers.ont_info');
    Route::patch('/customers/{customer}/acs-device', [CustomerController::class, 'updateAcsDevice'])->name('customers.acs_device');
    Route::post('/customers/{customer}/wifi', [CustomerController::class, 'updateWifi'])->name('customers.wifi.update');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::patch('/customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('customers.status');
    Route::post('/customers/generate-dummy', [CustomerController::class, 'generateDummy'])->name('customers.generate_dummy');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/generate-mass', [InvoiceController::class, 'generateMass'])->name('invoices.generate_mass');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'printPdf'])->name('invoices.pdf');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::patch('/invoices/{invoice}/payment-method', [InvoiceController::class, 'updatePaymentMethod'])->name('invoices.payment_method');

    Route::get('/isolir', [IsolationController::class, 'index'])->name('isolation.index');
    Route::post('/isolir/settings', [IsolationController::class, 'updateSettings'])->name('isolation.settings');
    Route::post('/isolir/run-auto', [IsolationController::class, 'runAutomatic'])->name('isolation.run_auto');
    Route::post('/isolir/customers/{customer}', [IsolationController::class, 'isolate'])->name('isolation.customers.isolate');
    Route::delete('/isolir/customers/{customer}', [IsolationController::class, 'release'])->name('isolation.customers.release');

    Route::get('/network', [RouterController::class, 'index'])->name('network.index');
    Route::post('/routers', [RouterController::class, 'store'])->name('routers.store');
    Route::put('/routers/{router}', [RouterController::class, 'update'])->name('routers.update');
    Route::delete('/routers/{router}', [RouterController::class, 'destroy'])->name('routers.destroy');
    Route::post('/routers/{router}/test', [RouterController::class, 'testConnection'])->name('routers.test');
    Route::get('/routers/{router}/live', [RouterController::class, 'liveData'])->name('routers.live');

    Route::post('/odps', [OdpController::class, 'store'])->name('odps.store');
    Route::put('/odps/{odp}', [OdpController::class, 'update'])->name('odps.update');
    Route::delete('/odps/{odp}', [OdpController::class, 'destroy'])->name('odps.destroy');

    Route::get('/pppoe-mapping', [PppoeMappingController::class, 'index'])->name('pppoe-mapping.index');
    Route::get('/pppoe-mapping/{router}/secrets', [PppoeMappingController::class, 'fetchSecrets'])->name('pppoe-mapping.secrets');
    Route::post('/pppoe-mapping/map', [PppoeMappingController::class, 'mapCustomer'])->name('pppoe-mapping.map');
    Route::delete('/pppoe-mapping/unmap/{customer}', [PppoeMappingController::class, 'unmapCustomer'])->name('pppoe-mapping.unmap');
    Route::post('/pppoe-mapping/auto-map', [PppoeMappingController::class, 'autoMap'])->name('pppoe-mapping.auto-map');
    Route::patch('/pppoe-mapping/mac-ont/{customer}', [PppoeMappingController::class, 'updateMacOnt'])->name('pppoe-mapping.mac-ont');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/refresh', [MonitoringController::class, 'refresh'])->name('monitoring.refresh');

    Route::get('/traffic', [TrafficMonitorController::class, 'index'])->name('traffic.index');
    Route::get('/traffic/{router}', [TrafficMonitorController::class, 'fetchTraffic'])->name('traffic.fetch');
    Route::get('/traffic/{router}/interface', [TrafficMonitorController::class, 'fetchInterfaceStats'])->name('traffic.interface');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{employee}/idcard', [EmployeeController::class, 'printIdCard'])->name('employees.idcard');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    Route::post('/payroll/pay-all', [PayrollController::class, 'payAll'])->name('payroll.pay_all');
    Route::post('/payroll/salary-config', [PayrollController::class, 'storeConfig'])->name('payroll.config.store');
    Route::delete('/payroll/salary-config/{salaryConfig}', [PayrollController::class, 'destroyConfig'])->name('payroll.config.destroy');
    Route::put('/payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::post('/payroll/{payroll}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
    Route::get('/payroll/{payroll}/pdf', [PayrollController::class, 'printPdf'])->name('payroll.pdf');
    Route::delete('/payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/news', [NewsAdminController::class, 'index'])->name('news.admin');
    Route::post('/news', [NewsAdminController::class, 'store'])->name('news.store');
    Route::post('/news/{news}', [NewsAdminController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [NewsAdminController::class, 'destroy'])->name('news.destroy');
    Route::delete('/news/comments/{comment}', [NewsAdminController::class, 'destroyComment'])->name('news.comment.destroy');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/expenses', [FinancialController::class, 'store'])->name('financial.expenses.store');
    Route::put('/financial/expenses/{expense}', [FinancialController::class, 'update'])->name('financial.expenses.update');
    Route::delete('/financial/expenses/{expense}', [FinancialController::class, 'destroy'])->name('financial.expenses.destroy');
});
