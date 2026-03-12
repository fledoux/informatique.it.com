<?php

namespace App\Http\Controllers;

use App\Models\WifiPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaMetricController extends Controller
{
    /**
     * Retourne le code WiFi Socrate avec authentification Bearer Token
     */
    public function displaySocrate(Request $request): JsonResponse
    {
        // Vérifier le token dans le header X-Access-Token
        // Essayer différents cas pour accéder au header
        $token = $request->header('X-Access-Token') 
            ?? $request->header('x-access-token')
            ?? $_SERVER['HTTP_X_ACCESS_TOKEN'] ?? null;
        
        $expectedToken = config('services.lametric.access_token');

        Log::debug('LaMetric: Token debug', [
            'header_x-Access-Token' => $request->header('X-Access-Token'),
            'header_x-access-token' => $request->header('x-access-token'),
            'server_http_x_access_token' => $_SERVER['HTTP_X_ACCESS_TOKEN'] ?? null,
            'final_token' => $token
        ]);

        if (!$token || $token !== $expectedToken) {
            Log::warning('LaMetric: Unauthorized access attempt', [
                'ip' => $request->ip(),
                'token_provided' => !!$token,
                'token_valid' => $token === $expectedToken,
                'expected' => $expectedToken
            ]);
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        // Récupérer le code WiFi depuis la base de données
        $wifiPassword = WifiPassword::latest('changed_at')->first();

        if (!$wifiPassword) {
            return response()->json([
                'error' => 'WiFi password not found'
            ], 404);
        }

        return response()->json([
            'frames' => [
                [
                    'text' => $wifiPassword->password
                ]
            ]
        ], 200);
    }
}
