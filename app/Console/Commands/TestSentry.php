<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSentry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentry:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Sentry error reporting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Sentry error reporting...');
        
        // Test d'une exception simple
        try {
            throw new \Exception('Test exception from Artisan command - ' . now());
        } catch (\Exception $e) {
            // Envoyer à Sentry
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
                $this->info('✅ Exception sent to Sentry successfully!');
            } else {
                $this->error('❌ Sentry is not configured properly');
            }
            
            // Log également
            Log::error('Test Sentry error', ['exception' => $e]);
        }
        
        // Test d'un message personnalisé
        if (app()->bound('sentry')) {
            app('sentry')->captureMessage('Test message from Laravel - ' . now());
            $this->info('✅ Message sent to Sentry successfully!');
        }
        
        $this->info('Test completed. Check your Sentry dashboard at https://sentry.io');
    }
}
