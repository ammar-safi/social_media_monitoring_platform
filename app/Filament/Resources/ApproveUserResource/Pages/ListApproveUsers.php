<?php

namespace App\Filament\Resources\ApproveUserResource\Pages;

use App\Enums\ApproveUserStatusEnum;
use App\Events\ApproveToAllUsersEvent;
use App\Filament\Resources\ApproveUserResource;
use App\Models\ApproveUser;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListApproveUsers extends ListRecords
{
    protected static string $resource = ApproveUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("Approve to all")

                ->requiresConfirmation()
                ->action(function () {
                    Notification::make()
                        ->info()
                        ->title("Approving ...")
                        ->body("This operation will take a few second")
                        ->send();
                    $admin_id = Filament::auth()->user()->id;
                    event(new ApproveToAllUsersEvent($admin_id));
                    // ApproveToAllUsersEvent::dispatch($admin_id);
                })

        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make("all"),
            'pending' => Tab::make("pending")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("status", ApproveUserStatusEnum::PENDING)
                        ->where("expired", 0);
                }),
            'approved' => Tab::make("approved")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("status", ApproveUserStatusEnum::APPROVED);
                }),
            'rejected' => Tab::make("rejected")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("status", ApproveUserStatusEnum::REJECTED);
                }),
            'expired' => Tab::make("expired")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("expired", 1);
                }),
        ];
    }
}
