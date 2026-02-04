<?php

namespace App\Filament\Resources\PolicyRequestResource\Pages;

use App\Enums\PolicyRequestEnum;
use App\Events\ApproveToAllUsersEvent;
use App\Filament\Resources\PolicyRequestResource;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;


class ListPolicyRequests extends ListRecords
{
    protected static string $resource = PolicyRequestResource::class;


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
                        ->where("status", PolicyRequestEnum::PENDING->value);
                }),
            'approved' => Tab::make("approved")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("status", PolicyRequestEnum::APPROVED->value);
                }),
            'rejected' => Tab::make("rejected")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("status", PolicyRequestEnum::REJECTED->value);
                }),
            'expired' => Tab::make("expired")
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where("status", PolicyRequestEnum::EXPIRED->value);
                }),
        ];
    }
}
