<?php

use App\Http\Controllers\Api\MobileCustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('/customer/login', [MobileCustomerController::class, 'login']);
    Route::get('/customer/invoices', [MobileCustomerController::class, 'invoices']);
    Route::get('/customer/invoices/{invoice}/pdf', [MobileCustomerController::class, 'customerInvoicePdf']);
    Route::get('/news', [MobileCustomerController::class, 'news']);

    Route::post('/staff/login', [MobileCustomerController::class, 'staffLogin']);
    Route::get('/staff/invoices', [MobileCustomerController::class, 'staffInvoices']);
    Route::patch('/staff/invoices/{invoice}/status', [MobileCustomerController::class, 'staffUpdateInvoiceStatus']);
    Route::patch('/staff/invoices/{invoice}/payment-method', [MobileCustomerController::class, 'staffUpdatePaymentMethod']);
    Route::get('/staff/invoices/{invoice}/pdf', [MobileCustomerController::class, 'staffInvoicePdf']);
});
