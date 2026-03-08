<?php

namespace App\Console\Commands;

use App\Services\UnifiService;
use Illuminate\Console\Command;

class TestUnifiConnectionCommand extends Command
{
    protected $signature = 'wifi:test-connection';
    protected $description = 'Tester la connexion à l\'API Unifi';

    public function handle(): int
    {
        $this->info('🔍 Test de connexion Unifi en cours...');

        $unifiService = new UnifiService();

        if ($unifiService->testConnection()) {
            $this->info('✓ Connexion Unifi réussie!');
            
            // Afficher les réseaux
            $this->line('');
            $this->info('📡 Réseaux disponibles:');
            $networks = $unifiService->getNetworks();
            
            if (empty($networks)) {
                $this->warn('⚠ Aucun réseau trouvé');
                return 0;
            }

            foreach ($networks as $network) {
                $name = $network['name'] ?? 'Unknown';
                $enabled = ($network['enabled'] ?? false) ? '✓' : '✗';
                $this->line("  {$enabled} {$name}");
            }

            return 0;
        }

        $this->error('✗ Erreur de connexion Unifi');
        $this->line('');
        $this->warn('Vérifiez:');
        $this->line('  1. UNIFI_CONTROLLER_URL est correct');
        $this->line('  2. UNIFI_API_KEY est valide');
        $this->line('  3. UNIFI_SITE_NAME existe');
        $this->line('  4. Le contrôleur est accessible');
        $this->line('');
        $this->line('Consultez le log: tail storage/logs/laravel.log');

        return 1;
    }
}
