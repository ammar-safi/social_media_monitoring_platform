<?php

namespace App\Observers;

use App\Events\EmailEvent;
use App\Models\User;
use Filament\Notifications\Notification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }


    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if (
            $user->active == 1
            && $user->getOriginal('active') == 0
            && $user->isDirty("active")
        ) {
            $message = "your account was reactivate";
            event(new EmailEvent($user, $message, "Reactivate"));
            Notification::make()
                ->success()
                ->title("Activated")
                ->body("An email was sent to " . $user->first_name)
                ->send();
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $user->update([
            "email" => $user->email . ".deleted"
        ]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
