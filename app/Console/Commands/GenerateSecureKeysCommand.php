<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSecureKeysCommand extends Command
{
    protected $signature = 'generate:secure-keys {--lines=132} {--length=32}';
    protected $description = 'Générer des clés aléatoires sécurisées (32 caractères sur 132 lignes)';

    public function handle(): int
    {
        $lines = (int) $this->option('lines');
        $length = (int) $this->option('length');

        $this->info("Génération de {$lines} clés sécurisées ({$length} caractères chacune)\n");

        for ($i = 1; $i <= $lines; $i++) {
            $key = $this->generateSecureKey($length);
            $this->line($key);
        }

        return 0;
    }

    /**
     * Générer une clé sécurisée avec min + maj + chiffres
     * Utilise random_bytes() pour une sécurité cryptographique
     */
    private function generateSecureKey(int $length = 32): string
    {
        // Caractères: A-Z (26) + a-z (26) + 0-9 (10) = 62 caractères
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charCount = strlen($characters);
        
        $key = '';
        
        // Utiliser random_bytes() pour une sécurité cryptographique
        for ($i = 0; $i < $length; $i++) {
            // random_int() est sécurisé pour la génération de nombres aléatoires
            $randomIndex = random_int(0, $charCount - 1);
            $key .= $characters[$randomIndex];
        }

        return $key;
    }
}
