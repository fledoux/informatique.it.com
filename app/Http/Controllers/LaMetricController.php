<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class LaMetricController extends Controller
{
    /**
     * Affiche le mot de passe WiFi Socrate sur LaMetric
     */
    public function displaySocrate(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'WiFi password displayed on LaMetric',
            'endpoint' => '/lametric/socrate'
        ], 200);
    }
}
