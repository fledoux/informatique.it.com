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
        $token = $request->header('X-Access-Token');
        $expectedToken = config('services.lametric.access_token');

        if (!$token || $token !== $expectedToken) {
            Log::warning('LaMetric: Unauthorized access attempt', [
                'ip' => $request->ip(),
                'token_provided' => !!$token,
                'token_valid' => $token === $expectedToken
            ]);
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        // Récupérer le code WiFi depuis la base de données
        $wifiPassword = WifiPassword::latest('changed_at')->first();

        Log::debug('LaMetric: WiFi password query', [
            'found' => !!$wifiPassword,
            'total_records' => WifiPassword::count(),
            'password' => $wifiPassword ? $wifiPassword->password : null
        ]);

        if (!$wifiPassword) {
            return response()->json([
                'error' => 'WiFi password not found'
            ], 404);
        }

        return response()->json([
            'frames' => [
                [
                    'text' => $wifiPassword->password,
                    'index' => 0
                ]
            ]
        ], 200);
    }
}
