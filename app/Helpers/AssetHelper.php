<?php

if (!function_exists('versioned_asset')) {
    /**
     * Génère un URL d'asset avec timestamp automatique
     */
    function versioned_asset(string $path): string
    {
        $fullPath = public_path($path);
        
        if (file_exists($fullPath)) {
            $timestamp = filemtime($fullPath);
            return asset($path) . '?v=' . $timestamp;
        }
        
        // Si le fichier n'existe pas, retourner l'asset normal
        return asset($path);
    }
}

if (!function_exists('asset_timestamp')) {
    /**
     * Alternative avec format personnalisé
     */
    function asset_timestamp(string $path, string $param = 'v'): string
    {
        $fullPath = public_path($path);
        
        if (file_exists($fullPath)) {
            $timestamp = filemtime($fullPath);
            return asset($path) . '?' . $param . '=' . $timestamp;
        }
        
        return asset($path);
    }
}