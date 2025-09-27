<?php

namespace App\Services;

use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoie un SMS via Amazon SNS
     */
    public static function send(string $phoneNumber, string $message): bool
    {
        try {
            $sns = new SnsClient([
                'region' => config('services.ses.region', 'eu-west-1'),
                'version' => 'latest',
                'credentials' => [
                    'key' => config('services.ses.key'),
                    'secret' => config('services.ses.secret'),
                ]
            ]);

            $sns->publish([
                'Message' => $message,
                'PhoneNumber' => $phoneNumber
            ]);

            Log::info('SMS envoyé avec succès', [
                'phone' => $phoneNumber
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}