<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * Verify Cloudflare Turnstile token
     *
     * @param string $token The token from the frontend
     * @param string|null $remoteIp User's IP address
     * @return array The verification response
     */
    public static function verify(string $token, ?string $remoteIp = null): array
    {
        $secretKey = config('services.turnstile.secret_key');

        if (!$secretKey) {
            // If no secret key, skip verification (local environment)
            return ['success' => true];
        }

        try {
            $response = Http::post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $remoteIp,
            ]);

            $data = $response->json();

            return [
                'success' => $data['success'] ?? false,
                'error_codes' => $data['error-codes'] ?? [],
                'challenge_ts' => $data['challenge_ts'] ?? null,
                'hostname' => $data['hostname'] ?? null,
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cloudflare Turnstile verification failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error_codes' => ['verification_failed'],
            ];
        }
    }
}
