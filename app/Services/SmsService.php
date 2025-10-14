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
                'region' => config('services.sns.region', 'eu-west-3'),
                'version' => 'latest',
                'credentials' => [
                    'key' => config('services.sns.key'),
                    'secret' => config('services.sns.secret'),
                ]
            ]);

            // Récupérer le Sender ID depuis la config (optionnel)
            $senderId = config('services.sns.sender_id');
            
            $params = [
                'SMSType' => 'Transactional',
                'Message' => $message,
                'PhoneNumber' => $phoneNumber,
            ];

            $result = $sns->publish($params);

            Log::info('SMS envoyé avec succès', [
                'phone' => $phoneNumber,
                'message_id' => $result->get('MessageId')
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
