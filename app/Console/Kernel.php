<?php

namespace App\Console;

use App\Jobs\ExtractPostsJob;
use App\Models\ApproveUser;
use App\Models\Invite;
use App\Models\PolicyRequest;
use App\Services\ExpirationService;
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
            app(ExpirationService::class)->CheckExpirationForGovRequest();
        })->daily();
        $schedule->call(function () {
            app(ExpirationService::class)->CheckExpirationForPolicyInvites();
        })->daily();
        $schedule->call(function () {
            app(ExpirationService::class)->CheckExpirationForPolicyRequest();
        })->daily();

        $schedule->job(new ExtractPostsJob)->daily();
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
