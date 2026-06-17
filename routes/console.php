<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Auto-update booking status (setiap hari pukul 00:05) ──────────────────────
// Mengaktifkan booking yang start_date-nya sudah tiba (confirmed → active)
// Menyelesaikan booking yang end_date-nya sudah lewat (active → completed)
Schedule::command('bookings:auto-update')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();
