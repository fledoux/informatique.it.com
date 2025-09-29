<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\PushoverService;

class Page extends Model
{
    /**
     * Envoie une notification Pushover pour le scan QR
     */
    public static function sendQrScanNotification(string $title = 'QR Code scanné'): void
    {
        $message = 'IP : ' . request()->ip() . "\n" . 'Date : ' . now()->format('d/m/Y') . "\n" . 'Heure : ' . now()->format('H:i:s');
        PushoverService::send($title, $message);
    }
}