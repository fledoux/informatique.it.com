<?php

namespace App\Console\Commands;

use App\Models\WifiPassword;
use App\Services\UnifiService;
use Illuminate\Console\Command;

class ChangeWifiPasswordCommand extends Command
{
    protected $signature = 'wifi:change-password {--ssid=test} {--length=12}';
    protected $description = 'Changer le mot de passe Wi-Fi Unifi';

    public function handle(): int
    {
        $ssid = $this->option('ssid');
        $requestedLength = (int) $this->option('length');

        // Unifi requiert minimum 8 caractères
        $generatedLength = max($requestedLength, 8);

        $this->info("Génération d'un nouveau mot de passe Wi-Fi pour: {$ssid}");

        // Générer un mot de passe sécurisé avec jour de la semaine en première position
        $dayOfWeek = date('N'); // 1=lundi, 7=dimanche
        $newPassword = $dayOfWeek . $this->generatePassword($generatedLength - 1);
        
        $dayNames = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $this->info("Nouveau mot de passe: {$newPassword} ({$dayNames[$dayOfWeek]})");

        // Changer sur Unifi
        $unifiService = new UnifiService();
        
        if (!$unifiService->changeWifiPassword($ssid, $newPassword)) {
            $this->error('❌ Erreur lors du changement sur Unifi');
            return 1;
        }

        $this->info('✓ Mot de passe changé sur Unifi');

        // Stocker en base de données UNIQUEMENT après confirmation Unifi
        WifiPassword::create([
            'network_name' => $ssid,
            'password' => $newPassword,
            'changed_at' => now(),
        ]);

        $this->info('✓ Mot de passe enregistré en base de données');

        // Supprimer les enregistrements plus anciens que 2 jours
        WifiPassword::where('created_at', '<', now()->subDays(2))->delete();
        $this->info('✓ Ancien mot de passe supprimé (conservation 2 jours)');

        $this->line('');
        $this->info('✓ Processus terminé avec succès');
        
        return 0;
    }

    /**
     * Générer un mot de passe sécurisé
     * Majuscules uniquement, sans caractères ambigus (I, L, O, S, 0, 1, 5)
     */
    private function generatePassword(int $length = 12): string
    {
        // Majuscules sans I, L, O, S (pour éviter confusion avec i, l, 0, s)
        // Chiffres sans 0, 1, 5 (pour éviter confusion avec O, I, S)
        $characters = 'ABCDEFGHJKMNPQRTUVWXYZ2346789';
        
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }
}
