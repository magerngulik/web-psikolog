<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup database terjadwal (mengecek konfigurasi frekuensi harian, mingguan, atau bulanan setiap pk 01:00)
Schedule::command('app:backup-database')->dailyAt('01:00');
