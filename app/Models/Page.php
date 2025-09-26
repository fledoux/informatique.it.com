<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\PushoverService;

class Page extends Model
{
    /**
     * Envoie une notification Pushover pour le scan QR
     */
    public static function sendQrScanNotification(): void
    {
        $title = 'QR Code scanné';
        $message = 'Depuis ' . request()->ip() . ' à ' . now()->format('H:i:s');
        
        PushoverService::send($title, $message);
    }
}