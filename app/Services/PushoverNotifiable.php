<?php

namespace App\Services;

use Illuminate\Notifications\Notifiable;

class PushoverNotifiable
{
    use Notifiable;

    /**
     * Route notifications for the Pushover channel.
     */
    public function routeNotificationForPushover(): string
    {
        return config('services.pushover.user');
    }
}