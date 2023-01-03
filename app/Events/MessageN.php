<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageN implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $user_id;

    public $notification_id;

    public $event_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($event_name, $user_id, $notification_id, $event_id)
    {
        $this->message  = "You received a new message from {$user_id} !";
        $this->user_id = $user_id;
        $this->notification_id = $notification_id;
        $this->event_id = $event_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['notifications-messages'];
    }

    public function broadcastAs() {
        return "event-message-{$this->user_id}";
    }
}