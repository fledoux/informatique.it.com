<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\MakeCrudBootstrap::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Appliquer SetLocale globalement pour toutes les requêtes web
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Traiter la queue toutes les minutes
        $schedule->command('queue:work', [
            '--max-jobs=1000',
            '--max-time=55',
            '--once'
        ])
            ->everyMinute()
            ->name('queue-worker')
            ->withoutOverlapping();

        // Changer le mot de passe Wi-Fi tous les jours à 5h
        $schedule->command('wifi:change-password --ssid=socrate_guest --length=8')
            ->dailyAt('05:00')
            ->name('wifi-password-change')
            ->onOneServer();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Capturer les sessions expirées (CSRF Token mismatch)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect('/login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        });
        
        // Capturer les exceptions Spatie Permission
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, \Illuminate\Http\Request $request) {
            return redirect('/login')->with('error', 'Accès refusé. Permissions insuffisantes.');
        });
    })->create();
