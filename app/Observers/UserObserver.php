<?php

namespace App\Observers;

use App\Enums\UserTypeEnum;
use App\Events\NotifyUserEvent;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UserObserver implements ShouldHandleEventsAfterCommit
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
            event(new NotifyUserEvent(
                user_name: $user->name,
                email: $user->email,
                subject: "Reactivate",
                message: $message
            ));
            Notification::make()
                ->success()
                ->icon("heroicon-o-check-circle")
                ->title("Activated")
                ->body("An email was sent to " . $user->first_name)
                ->send();
        }
    }

    public function saved(User $user)
    {
        if ($user->wasRecentlyCreated || $user->wasChanged('type')) {
            $user->assignRoleBasedOnType();
        }
    }

    public function saving(User $user)
    {
        if ($user->type == null) {
            $user->type = 'user';
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

    public function deleting(User $user)
    {
        if ($user->type === UserTypeEnum::ADMIN) {
            throw new \Exception('you cannot delete this admin');
        }
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
