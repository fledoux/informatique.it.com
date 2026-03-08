<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LaMetricService
{
    private $deviceIp;
    private $devicePort = 4343;
    private $accessToken;
    private $widgetId;
    private $frameId = 2;  // WiFi app frame 2

    public function __construct()
    {
        $this->accessToken = config('services.lametric.access_token', '');
        $this->deviceIp = config('services.lametric.device_ip', '');
        $this->widgetId = config('services.lametric.widget_id', '');
    }

    /**
     * Envoyer le mot de passe Wi-Fi à LaMetric
     * Using LaMetric v1 API: POST https://{device_ip}:4343/api/v1/dev/widget/update/{widget_id}/{frame_id}
     * Header: X-Access-Token: {token}
     */
    public function displayWifiPassword(string $password): bool
    {
        try {
            if (!$this->deviceIp || !$this->accessToken || !$this->widgetId) {
                Log::error('LaMetric: Configuration missing', [
                    'device_ip' => !!$this->deviceIp,
                    'access_token' => !!$this->accessToken,
                    'widget_id' => !!$this->widgetId
                ]);
                return false;
            }

            // Afficher le mot de passe complet sur LaMetric
            $displayPassword = $password;

            // Créer le payload selon la v1 API
            $payload = [
                'frames' => [
                    [
                        'text' => "{$displayPassword}",
                        'index' => 0
                    ]
                ]
            ];

            $response = Http::acceptJson()
                ->withHeader('X-Access-Token', $this->accessToken)
                ->withHeader('Cache-Control', 'no-cache')
                ->withoutVerifying()
                ->timeout(10)
                ->post(
                    "https://{$this->deviceIp}:{$this->devicePort}/api/v1/dev/widget/update/{$this->widgetId}/{$this->frameId}",
                    $payload
                );

            if ($response->successful()) {
                Log::info('LaMetric: Password displayed successfully', ['password' => substr($displayPassword, 0, 3) . '***']);
                return true;
            }

            Log::error('LaMetric: Failed to display password', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('LaMetric: Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Afficher une notification personnalisée
     */
    public function notify(string $message): bool
    {
        try {
            if (!$this->deviceIp || !$this->accessToken || !$this->widgetId) {
                Log::error('LaMetric: Configuration missing');
                return false;
            }

            $payload = [
                'frames' => [
                    [
                        'text' => $message,
                        'index' => 0
                    ]
                ]
            ];

            $response = Http::acceptJson()
                ->withHeader('X-Access-Token', $this->accessToken)
                ->withHeader('Cache-Control', 'no-cache')
                ->withoutVerifying()
                ->timeout(10)
                ->post(
                    "https://{$this->deviceIp}:{$this->devicePort}/api/v1/dev/widget/update/{$this->widgetId}/{$this->frameId}",
                    $payload
                );

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('LaMetric: Notification exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
