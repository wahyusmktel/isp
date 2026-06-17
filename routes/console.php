<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\IsolationController;
use App\Services\MikrotikService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('isolir:apply', function () {
    $response = app(IsolationController::class)->runAutomatic(app(MikrotikService::class));
    $payload = $response->getData(true);

    if (($payload['success'] ?? false) === true) {
        $this->info($payload['message'] ?? 'Proses isolir otomatis selesai.');
        return 0;
    }

    $this->error($payload['message'] ?? 'Proses isolir otomatis gagal.');
    foreach (($payload['failed'] ?? []) as $failed) {
        $this->warn($failed);
    }

    return 1;
})->purpose('Apply automatic customer isolation when enabled');
