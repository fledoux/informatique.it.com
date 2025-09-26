<?php

namespace App\Services;

use App\Notifications\PushoverNotification;
use App\Services\PushoverNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class PushoverService
{
    /**
     * Envoie une notification Pushover
     */
    public static function send(string $title, string $message, int $priority = 0): bool
    {
        try {
            // Vérifier que Pushover est configuré
            if (!config('services.pushover.token') || !config('services.pushover.user')) {
                Log::info('Pushover not configured - notification skipped', [
                    'title' => $title,
                    'message' => $message
                ]);
                return false;
            }

            // Créer un objet notifiable avec les credentials Pushover
            $notifiable = new PushoverNotifiable();

            // Envoyer la notification
            $notifiable->notify(new PushoverNotification($title, $message, $priority));
            
            Log::info('Pushover notification sent successfully', [
                'title' => $title
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Pushover notification failed', [
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Envoie une notification de scan QR code
     */
    public static function sendQrScanNotification(string $ip = null): bool
    {
        $ip = $ip ?: request()->ip();
        $title = 'QR Code scanné';
        $message = 'Depuis ' . $ip . ' à ' . now()->format('H:i:s');
        
        return self::send($title, $message);
    }
}