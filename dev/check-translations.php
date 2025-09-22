#!/usr/bin/env php
<?php

/**
 * Script de vérification des traductions manquantes
 */

$basePath = dirname(__DIR__); // Remonter au dossier parent (racine du projet)

// Charger les fichiers de traduction
$frTranslations = [];
$enTranslations = [];

$frPath = $basePath . '/resources/lang/fr';
$enPath = $basePath . '/resources/lang/en';

if (is_dir($frPath)) {
    foreach (glob($frPath . '/*.php') as $file) {
        $key = basename($file, '.php');
        $frTranslations[$key] = include $file;
    }
}

if (is_dir($enPath)) {
    foreach (glob($enPath . '/*.php') as $file) {
        $key = basename($file, '.php');
        $enTranslations[$key] = include $file;
    }
}

// Fonction pour extraire les clés de traduction d'un fichier
function extractTranslationKeys($content) {
    $keys = [];
    // Pattern pour __('key') et __("key")
    preg_match_all("/__\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
    foreach ($matches[1] as $key) {
        $keys[] = $key;
    }
    return $keys;
}

// Scanner tous les fichiers PHP et Blade
$files = array_merge(
    glob($basePath . '/resources/views/**/*.blade.php', GLOB_BRACE),
    glob($basePath . '/app/Http/Controllers/**/*.php', GLOB_BRACE),
    glob($basePath . '/app/Http/Requests/**/*.php', GLOB_BRACE)
);

$allKeys = [];
foreach ($files as $file) {
    $content = file_get_contents($file);
    $keys = extractTranslationKeys($content);
    foreach ($keys as $key) {
        $allKeys[] = $key;
        $allKeysWithFiles[$key][] = basename($file);
    }
}

$allKeys = array_unique($allKeys);

// Vérifier les clés manquantes
$missingFr = [];
$missingEn = [];

// Function to check if a nested key exists in an array
function hasNestedKey($array, $key) {
    $keys = explode('.', $key);
    $current = $array;
    
    foreach ($keys as $k) {
        if (!is_array($current) || !array_key_exists($k, $current)) {
            return false;
        }
        $current = $current[$k];
    }
    
    return true;
}

foreach ($allKeys as $key) {
    if (strpos($key, '.') !== false) {
        list($file, $subkey) = explode('.', $key, 2);
        
        // Vérifier en français
        if (!isset($frTranslations[$file])) {
            $missingFr[] = $key;
        } else {
            // Si la sous-clé ne contient pas de point (sauf à la fin), c'est une clé de premier niveau
            $trimmedSubkey = rtrim($subkey, '.');
            if (strpos($trimmedSubkey, '.') === false) {
                if (!array_key_exists($subkey, $frTranslations[$file])) {
                    $missingFr[] = $key;
                }
            } else {
                // Clé imbriquée
                if (!hasNestedKey($frTranslations[$file], $subkey)) {
                    $missingFr[] = $key;
                }
            }
        }
        
        // Vérifier en anglais
        if (!isset($enTranslations[$file])) {
            $missingEn[] = $key;
        } else {
            // Si la sous-clé ne contient pas de point (sauf à la fin), c'est une clé de premier niveau
            $trimmedSubkey = rtrim($subkey, '.');
            if (strpos($trimmedSubkey, '.') === false) {
                if (!array_key_exists($subkey, $enTranslations[$file])) {
                    $missingEn[] = $key;
                }
            } else {
                // Clé imbriquée
                if (!hasNestedKey($enTranslations[$file], $subkey)) {
                    $missingEn[] = $key;
                }
            }
        }
    }
}

// Afficher les résultats
echo "🔍 VÉRIFICATION DES TRADUCTIONS\n";
echo "================================\n\n";

echo "📊 Statistiques:\n";
echo "- Clés de traduction trouvées: " . count($allKeys) . "\n";
echo "- Fichiers de traduction FR: " . count($frTranslations) . "\n";
echo "- Fichiers de traduction EN: " . count($enTranslations) . "\n\n";

if (!empty($missingFr)) {
    echo "❌ TRADUCTIONS MANQUANTES EN FRANÇAIS:\n";
    foreach ($missingFr as $key) {
        echo "  - $key (utilisé dans: " . implode(', ', array_unique($allKeysWithFiles[$key] ?? [])) . ")\n";
    }
    echo "\n";
}

if (!empty($missingEn)) {
    echo "❌ TRADUCTIONS MANQUANTES EN ANGLAIS:\n";
    foreach ($missingEn as $key) {
        echo "  - $key (utilisé dans: " . implode(', ', array_unique($allKeysWithFiles[$key] ?? [])) . ")\n";
    }
    echo "\n";
}

if (empty($missingFr) && empty($missingEn)) {
    echo "✅ Toutes les traductions sont présentes!\n";
} else {
    echo "⚠️  Traductions à ajouter: " . (count($missingFr) + count($missingEn)) . "\n";
}

echo "\n📋 TOUTES LES CLÉS UTILISÉES:\n";
foreach (array_unique($allKeys) as $key) {
    echo "  - $key\n";
}