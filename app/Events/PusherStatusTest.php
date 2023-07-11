<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PusherStatusTest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $name)
    {
    }

    public function broadcastOn()
    {
        return new Channel('pusher-status');
    }

    // public function broadcastWith(): array
    // {
    //     return [
    //         "Testing Pusher connection from inside Laravel",
    //     ];
    // }
}
