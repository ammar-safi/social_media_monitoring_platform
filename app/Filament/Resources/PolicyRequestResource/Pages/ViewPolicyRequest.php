<?php

namespace App\Filament\Resources\PolicyRequestResource\Pages;

use App\Enums\PolicyRequestEnum;
use App\Filament\Resources\PolicyRequestResource;
use App\Models\PolicyRequest;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPolicyRequest extends ViewRecord
{
    protected static string $resource = PolicyRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("approve")
                ->requiresConfirmation()
                ->color("primary")
                ->action(function (PolicyRequest $approve, $record) {
                    if ($approve->approve()) {
                        Notification::make()
                            ->success()
                            ->icon("heroicon-o-check-circle")
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
                    if ($record->status != PolicyRequestEnum::PENDING) {
                        return true;
                    }
                    return false;
                }),
            Action::make("delete")
                ->requiresConfirmation()
                ->modalIconColor("danger")
                ->modalIcon("heroicon-o-trash")
                ->color("gray")
                ->action(function ($record) {
                    $record->delete();
                    Notification::make()
                        ->danger()
                        ->icon("heroicon-o-trash")
                        ->title("Deleted")
                        ->body("the request deleted successfully")
                        ->send();
                    $this->redirect(PolicyRequestResource::getUrl("index"));
                }),

            Action::make("reject")
                ->requiresConfirmation()
                ->modalIconColor("warning")
                ->modalIcon("heroicon-o-no-symbol")
                ->color("gray")
                ->action(function (PolicyRequest $approve, $record) {
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
                    if ($record->status != PolicyRequestEnum::PENDING) {
                        return true;
                    }
                    return false;
                }),
        ];
    }
}
