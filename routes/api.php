<?php

use App\Http\Controllers\Api\MobileCustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('/customer/login', [MobileCustomerController::class, 'login']);
    Route::get('/customer/invoices', [MobileCustomerController::class, 'invoices']);
    Route::get('/news', [MobileCustomerController::class, 'news']);
});
