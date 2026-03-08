<?php

namespace App\Console\Commands;

use App\Models\WifiPassword;
use Illuminate\Console\Command;

class ShowWifiPasswordHistoryCommand extends Command
{
    protected $signature = 'wifi:history {--limit=10}';
    protected $description = 'Afficher l\'historique des mots de passe Wi-Fi';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        
        $passwords = WifiPassword::orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();

        if ($passwords->isEmpty()) {
            $this->warn('Aucun mot de passe enregistré');
            return 0;
        }

        $this->info("📋 Historique des {$passwords->count()} derniers mots de passe:");
        $this->line('');

        $headers = ['Network', 'Password', 'Date de changement'];
        $rows = $passwords->map(function ($pwd) {
            return [
                $pwd->network_name,
                $pwd->password,
                $pwd->changed_at->format('d/m/Y H\hi'),
            ];
        })->toArray();

        $this->table($headers, $rows);

        return 0;
    }
}
