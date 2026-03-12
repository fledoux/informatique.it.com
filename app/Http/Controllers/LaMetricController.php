<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class LaMetricController extends Controller
{
    /**
     * Retourne le payload attendu par LaMetric
     */
    public function displaySocrate(): JsonResponse
    {
        return response()->json([
            'frames' => [
                [
                    'text' => 'WiFi Ready',
                    'index' => 0
                ]
            ]
        ], 200);
    }
}
