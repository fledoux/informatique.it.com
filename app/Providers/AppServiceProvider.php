<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding pour le service Pushover
        $this->app->when(\NotificationChannels\Pushover\Pushover::class)
            ->needs('$token')
            ->give(function () {
                return config('services.pushover.token');
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Eager load company relation for authenticated users
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                if ($user && !$user->relationLoaded('company')) {
                    $user->load('company');
                }
            }
        });
    }
}
