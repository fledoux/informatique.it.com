<?php

namespace App\Console\Commands;

use App\Models\WifiPassword;
use App\Services\LaMetricService;
use Illuminate\Console\Command;

class PushWifiPasswordCommand extends Command
{
    protected $signature = 'wifi:push-password {--ssid=socrate_guest}';
    protected $description = 'Envoyer le mot de passe du jour sur LaMetric';

    public function handle(): int
    {
        $ssid = $this->option('ssid');
        
        // Récupérer le dernier mot de passe du jour
        $today = now()->toDateString();
        $password = WifiPassword::where('network_name', $ssid)
            ->whereDate('changed_at', $today)
            ->latest('changed_at')
            ->value('password');

        if (!$password) {
            $this->warn("Aucun mot de passe pour {$ssid} aujourd'hui");
            return 1;
        }

        $dayOfWeek = date('N'); // 1=lundi, 7=dimanche
        $dayNames = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        $this->info("Envoi du mot de passe LaMetric - {$dayNames[$dayOfWeek]} ({$dayOfWeek})");

        // Afficher sur LaMetric
        $laMetricService = new LaMetricService();
        
        if ($laMetricService->displayWifiPassword($password)) {
            $this->info("✓ Mot de passe envoyé à LaMetric: {$password}");
            return 0;
        } else {
            $this->error("✗ Erreur lors de l'envoi à LaMetric");
            return 1;
        }
    }
}
