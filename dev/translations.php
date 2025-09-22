#!/usr/bin/env php
<?php

/**
 * Script de convenance pour la gestion des traductions
 * Usage: php dev/translations.php [check|fix|help]
 */

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'check':
        echo "🔍 Vérification des traductions...\n";
        passthru('php ' . __DIR__ . '/check-translations.php');
        break;
        
    case 'fix':
        echo "🔧 Correction automatique des traductions...\n";
        passthru('php ' . __DIR__ . '/fix-translations.php');
        echo "\n";
        echo "🔍 Vérification après correction...\n";
        passthru('php ' . __DIR__ . '/check-translations.php');
        break;
        
    case 'help':
    default:
        echo "🌐 Gestionnaire de traductions Laravel\n";
        echo "=====================================\n\n";
        echo "Usage: php dev/translations.php [command]\n\n";
        echo "Commandes disponibles:\n";
        echo "  check    Vérifier les traductions manquantes\n";
        echo "  fix      Corriger automatiquement les traductions puis vérifier\n";
        echo "  help     Afficher cette aide\n\n";
        echo "Exemples:\n";
        echo "  php dev/translations.php check\n";
        echo "  php dev/translations.php fix\n\n";
        echo "📊 État actuel:\n";
        passthru('php ' . __DIR__ . '/check-translations.php | head -n 8');
        break;
}