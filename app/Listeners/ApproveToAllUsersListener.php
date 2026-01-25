<?php

namespace App\Listeners;

use App\Events\ApproveToAllUsersEvent;
use App\Models\ApproveUser;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ApproveToAllUsersListener implements ShouldQueue
{
    public function handle(ApproveToAllUsersEvent $event): void
    {
        $approve = new ApproveUser();
        \Log::info("hello from listener");
        $approve->approveAll($event->admin_id);
    }
}
