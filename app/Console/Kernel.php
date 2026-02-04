<?php

namespace App\Console;

use App\Models\ApproveUser;
use App\Models\Invite;
use App\Models\PolicyRequest;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            ApproveUser::CheckExpiration(); 
        })->daily();

        $schedule->call(function () {
            Invite::CheckExpiration();
        })->daily();
        
        $schedule->call(function () {
            PolicyRequest::CheckExpiration();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
