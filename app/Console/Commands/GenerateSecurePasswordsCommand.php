<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSecurePasswordsCommand extends Command
{
    protected $signature = 'generate:passwords {--lines=132} {--length=32} {--export=}';
    protected $description = 'Générer des mots de passe très sécurisés (alphanumériques maj/min)';

    public function handle(): int
    {
        $lines = (int) $this->option('lines');
        $length = (int) $this->option('length');
        $export = $this->option('export');

        $this->info("Génération de {$lines} mots de passe de {$length} caractères...\n");

        $passwords = [];
        
        for ($i = 0; $i < $lines; $i++) {
            // Générer un mot de passe très sécurisé avec maj/min/chiffres
            $password = $this->generateSecurePassword($length);
            $passwords[] = $password;
            
            // Afficher chaque mot de passe numéroté
            $this->line(sprintf('%3d: %s', $i + 1, $password));
        }

        $this->newLine();
        $this->info("✓ {$lines} mots de passe générés avec succès");

        // Exporter en fichier si demandé
        if ($export) {
            $this->exportToFile($passwords, $export);
        }

        return 0;
    }

    /**
     * Générer un mot de passe sécurisé avec crypto-random
     * Contient: A-Z, a-z, 0-9
     */
    private function generateSecurePassword(int $length = 32): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            // Utiliser random_int() pour plus de sécurité
            $randomIndex = random_int(0, strlen($characters) - 1);
            $password .= $characters[$randomIndex];
        }

        return $password;
    }

    /**
     * Exporter les mots de passe dans un fichier
     */
    private function exportToFile(array $passwords, string $filename): void
    {
        $content = implode("\n", $passwords);
        $filepath = storage_path("app/{$filename}");
        
        file_put_contents($filepath, $content);
        
        $this->newLine();
        $this->info("✓ Mots de passe exportés vers: {$filepath}");
        $this->warn("⚠️  Fichier contenant les mots de passe - À sécuriser!");
    }
}
