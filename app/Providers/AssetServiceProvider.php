<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Directive Blade personnalisée pour les assets avec timestamp
        Blade::directive('asset_versioned', function ($expression) {
            return "<?php 
                \$path = $expression;
                \$fullPath = public_path(\$path);
                \$timestamp = file_exists(\$fullPath) ? filemtime(\$fullPath) : time();
                echo asset(\$path) . '?v=' . \$timestamp;
            ?>";
        });
        
        // Directive pour CSS avec timestamp
        Blade::directive('css', function ($expression) {
            return "<?php 
                \$path = $expression;
                \$fullPath = public_path(\$path);
                \$timestamp = file_exists(\$fullPath) ? filemtime(\$fullPath) : time();
                echo '<link rel=\"stylesheet\" href=\"' . asset(\$path) . '?v=' . \$timestamp . '\">';
            ?>";
        });
        
        // Directive pour JS avec timestamp
        Blade::directive('js', function ($expression) {
            return "<?php 
                \$path = $expression;
                \$fullPath = public_path(\$path);
                \$timestamp = file_exists(\$fullPath) ? filemtime(\$fullPath) : time();
                echo '<script src=\"' . asset(\$path) . '?v=' . \$timestamp . '\"></script>';
            ?>";
        });
    }
}
