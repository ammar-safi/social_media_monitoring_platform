<?php

namespace App\Listeners;

use App\Events\ApproveToAllUsersEvent;
use App\Models\ApproveUser;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ApproveToAllUsersListener implements ShouldQueue
{
    public function handle(ApproveToAllUsersEvent $event): void
    {
        $approve = new ApproveUser();

        Log::info("------ Approving to all users ------");
        $approve->approveAll($event->admin_id);
        Log::info("--------------- DONE ---------------");
    }
}
