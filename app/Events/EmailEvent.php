<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $subject;
    public $email;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, $message, $subject = "New message", $email = null)
    {
        $this->message = $message;
        $this->subject = $subject;
        $this->email = $email;
    }
}
