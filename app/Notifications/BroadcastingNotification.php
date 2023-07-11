<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BroadcastingNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public string $message)
    {}

    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message
        ]);
    }

    public function broadcastType(): string
    {
        return 'broadcast.notification';
    }
}
