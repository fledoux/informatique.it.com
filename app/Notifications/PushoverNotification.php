<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;

class PushoverNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $title;
    public string $message;
    public int $priority;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, int $priority = 0)
    {
        $this->title = $title;
        $this->message = $message;
        $this->priority = $priority;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [PushoverChannel::class];
    }

    /**
     * Get the Pushover representation of the notification.
     */
    public function toPushover($notifiable): PushoverMessage
    {
        return PushoverMessage::create($this->message)
            ->title($this->title)
            ->priority($this->priority)
            ->sound('pushover'); // Son par défaut
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
