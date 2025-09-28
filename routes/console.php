<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily database backup at 2:00 AM KST (17:00 UTC)
Schedule::command('backup:database')
    ->dailyAt('17:00')
    ->timezone('Asia/Seoul')
    ->appendOutputTo(storage_path('logs/backup.log'));

// Also create JSON backup
Schedule::command('backup:database --json')
    ->dailyAt('17:00')
    ->timezone('Asia/Seoul')
    ->appendOutputTo(storage_path('logs/backup.log'));
