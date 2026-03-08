<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnifiService
{
    private $baseUrl;
    private $apiKey;
    private $siteName;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.unifi.controller_url', 'https://192.168.111.35'), '/');
        $this->apiKey = config('services.unifi.api_key', '');
        $this->siteName = config('services.unifi.site_name', 'default');
    }

    /**
     * Changer le mot de passe Wi-Fi
     */
    public function changeWifiPassword(string $ssid, string $newPassword): bool
    {
        try {
            // Récupérer le réseau existant
            $network = $this->getNetwork($ssid);
            
            if (!$network) {
                Log::error('Unifi: Réseau non trouvé', ['ssid' => $ssid]);
                return false;
            }

            $networkId = $network['_id'] ?? null;
            
            if (!$networkId) {
                Log::error('Unifi: ID réseau manquant', ['ssid' => $ssid]);
                return false;
            }

            // Mettre à jour via l'API REST - Endpoint: /proxy/network/api/s/{site}/rest/wlanconf/{id}
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()
            ->put(
                "{$this->baseUrl}/proxy/network/api/s/{$this->siteName}/rest/wlanconf/{$networkId}",
                ['x_passphrase' => $newPassword]
            );

            if ($response->successful()) {
                Log::info('Unifi: Mot de passe changé avec succès', ['ssid' => $ssid]);
                return true;
            }

            Log::error('Unifi: Erreur lors du changement', [
                'ssid' => $ssid,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Unifi: Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Récupérer tous les réseaux Wi-Fi
     */
    public function getNetworks(): array
    {
        try {
            // Endpoint: /proxy/network/api/s/{site}/rest/wlanconf
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->withoutVerifying()
            ->get("{$this->baseUrl}/proxy/network/api/s/{$this->siteName}/rest/wlanconf");

            if ($response->successful()) {
                $data = $response->json();
                // Extraire le tableau 'data' de la réponse
                if (is_array($data) && isset($data['data'])) {
                    return $data['data'];
                } elseif (is_array($data)) {
                    return $data;
                }
                return [];
            }

            Log::error('Unifi: Erreur lors de la récupération des réseaux', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return [];

        } catch (\Exception $e) {
            Log::error('Unifi: Exception lors de la récupération', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Récupérer un réseau spécifique
     */
    public function getNetwork(string $ssid): ?array
    {
        $networks = $this->getNetworks();
        
        foreach ($networks as $network) {
            if (($network['name'] ?? null) === $ssid) {
                return $network;
            }
        }
        
        return null;
    }

    /**
     * Tester la connexion à l'API Unifi
     */
    public function testConnection(): bool
    {
        try {
            // Test avec l'endpoint de récupération des sites
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->withoutVerifying()
                ->get("{$this->baseUrl}/proxy/network/integration/v1/sites");

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Unifi: Erreur de connexion', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
