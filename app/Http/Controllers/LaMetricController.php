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
        // Récupérer le Bearer Token depuis le header Authorization
        $token = $request->bearerToken();
        $expectedToken = config('services.lametric.access_token');

        if (!$token || $token !== $expectedToken) {
            Log::warning('LaMetric: Unauthorized access attempt', [
                'ip' => $request->ip(),
                'token_provided' => !!$token
            ]);
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        // Récupérer le code WiFi depuis la base de données
        $wifiPassword = WifiPassword::latest('changed_at')->first();

        if (!$wifiPassword) {
            Log::error('LaMetric: WiFi password not found in database', [
                'ip' => $request->ip()
            ]);
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
