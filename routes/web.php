<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings');
Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

Route::get('/packages', [\App\Http\Controllers\PackageController::class, 'index'])->name('packages.index');
Route::post('/packages', [\App\Http\Controllers\PackageController::class, 'store'])->name('packages.store');
Route::put('/packages/{package}', [\App\Http\Controllers\PackageController::class, 'update'])->name('packages.update');
Route::delete('/packages/{package}', [\App\Http\Controllers\PackageController::class, 'destroy'])->name('packages.destroy');
Route::patch('/packages/{package}/toggle', [\App\Http\Controllers\PackageController::class, 'toggleStatus'])->name('packages.toggle');

Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');
Route::patch('/customers/{customer}/status', [\App\Http\Controllers\CustomerController::class, 'updateStatus'])->name('customers.status');

Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
Route::post('/invoices/generate-mass', [\App\Http\Controllers\InvoiceController::class, 'generateMass'])->name('invoices.generate_mass');
Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class, 'printPdf'])->name('invoices.pdf');
Route::post('/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
Route::put('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
Route::delete('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
Route::patch('/invoices/{invoice}/status', [\App\Http\Controllers\InvoiceController::class, 'updateStatus'])->name('invoices.status');
