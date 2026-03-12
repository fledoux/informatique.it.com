<?php

namespace App\Http\Controllers;

use App\Models\WifiPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaMetricController extends Controller
{
    /**
     * Retourne le code WiFi Socrate avec authentification Bearer Token
     */
    public function displaySocrate(Request $request): JsonResponse
    {
        // Vérifier le Bearer Token
        $token = $request->bearerToken();
        $expectedToken = config('services.lametric.access_token');

        if (!$token || $token !== $expectedToken) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        // Récupérer le code WiFi depuis la base de données
        $wifiPassword = WifiPassword::getLatest();

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
