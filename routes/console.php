<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('coupons:clean-expired')
    ->dailyAt('02:00')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('coupons:clean-expired failed');
    });

Schedule::command('purchases:report-stuck')
    ->hourly();

Schedule::command('reports:daily-sales')
    ->dailyAt('06:00');
