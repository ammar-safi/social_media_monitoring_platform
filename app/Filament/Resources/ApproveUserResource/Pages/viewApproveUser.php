<?php

namespace App\Filament\Resources\ApproveUserResource\Pages;

use App\Enums\ApproveUserStatusEnum;
use App\Filament\Resources\ApproveUserResource;
use App\Models\ApproveUser;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class viewApproveUser extends ViewRecord
{
    protected static string $resource = ApproveUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make("approve")
                ->requiresConfirmation()
                ->color("primary")
                ->action(function (ApproveUser $approve , $record) {
                    if ($approve->approve()) {
                        Notification::make()
                            ->success()
                            ->icon("heroicon-o-check")
                            ->title("Approved")
                            ->body("account approved and an email sent to " . $record->user?->first_name)
                            ->send();
                    } else {

                        Notification::make()
                            ->warning()
                            ->title("Error")
                            ->body("pleas try again")
                            ->send();
                    }
                })
                ->hidden(function ($record) {
                    if ($record->status != ApproveUserStatusEnum::PENDING->value) {
                        return true;
                    }
                    if ($record->expired) {
                        return true;
                    }
                    return false;
                }),
            Action::make("delete")
                ->requiresConfirmation()
                ->modalIconColor("danger")
                ->modalIcon("heroicon-o-trash")
                ->color("gray")
                // ->icon("heroicon-o-trash")
                ->action(function ($record) {
                    $record->delete();
                    Notification::make()
                        ->danger()
                        ->icon("heroicon-o-trash")
                        ->title("Deleted")
                        ->body("the request deleted successfully")
                        ->send();
                    $this->redirect(ApproveUserResource::getUrl("index"));
                }),

            Action::make("reject")
                // ->icon("heroicon-o-x-circle")
                ->requiresConfirmation()
                ->modalIconColor("warning")
                ->modalIcon("heroicon-o-no-symbol")
                ->color("gray")
                ->action(function (ApproveUser $approve , $record) {
                    if ($approve->reject()) {
                        Notification::make()
                            ->danger()
                            ->icon("heroicon-o-x-circle")
                            ->title("Rejected")
                            ->body("account rejected and an email sent to " . $record->user?->first_name)
                            ->send();
                    } else {
                        Notification::make()
                            ->warning()
                            ->title("Error")
                            ->body("pleas try again")
                            ->send();
                    }
                })
                ->hidden(function ($record) {
                    if ($record->status != ApproveUserStatusEnum::PENDING->value) {
                        return true;
                    }
                    if ($record->expired) {
                        return true;
                    }
                    return false;
                }),




        ];
    }
}
