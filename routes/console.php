<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// SCHEDULER TASKS
// ============================================

// Récupère les emails IMAP toutes les minutes
Schedule::command('email:fetch')
    ->everyMinute()
    ->withoutOverlapping(5) // Évite les chevauchements (timeout 5 min)
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Traite la queue toutes les minutes (une seule fois, puis s'arrête)
Schedule::command('queue:work', ['--once', '--sleep=3', '--tries=3'])
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Change le mot de passe WiFi tous les jours à 1h00
Schedule::command('wifi:change-password')
    ->dailyAt('01:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));
