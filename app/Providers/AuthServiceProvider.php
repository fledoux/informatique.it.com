<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        // VIDE - ne pas déclarer de policies
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}