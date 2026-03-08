<?php

namespace App\Console\Commands;

use App\Services\LaMetricService;
use Illuminate\Console\Command;

class TestLaMetricCommand extends Command
{
    protected $signature = 'wifi:test-lametric';
    protected $description = 'Tester l\'affichage sur LaMetric';

    public function handle(): int
    {
        $this->info('📊 Test LaMetric en cours...');

        $laMetricService = new LaMetricService();

        $testPassword = 'TestPass1Xy';
        $this->line("Essai d'affichage: {$testPassword}");

        if ($laMetricService->displayWifiPassword($testPassword)) {
            $this->info('✓ Affichage réussi sur LaMetric!');
            return 0;
        }

        $this->error('✗ Erreur LaMetric');
        $this->line('');
        $this->warn('Vérifiez:');
        $this->line('  1. LAMETRIC_API_KEY est valide');
        $this->line('  2. LAMETRIC_DEVICE_ID est correct');
        $this->line('  3. Le dispositif est connecté');
        $this->line('');
        $this->line('Consultez le log: tail storage/logs/laravel.log');

        return 1;
    }
}
