<?php

namespace App\Events;

use App\Models\ApproveUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApproveToAllUsersEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $admin_id;
    /**
     * Create a new event instance.
     */
    public function __construct(int $admin_id)
    {
        $this->admin_id = $admin_id;
    }
}
