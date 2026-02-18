<?php

namespace App\Filament\Resources\AnalystResource\Pages;

use App\Filament\Resources\AnalystResource;
use App\Jobs\Analysis\DispatchAnalysisJob;
use App\Jobs\ExtractPostsJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAnalysts extends ListRecords
{
    protected static string $resource = AnalystResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("Analyze new posts")
                ->action(function () {
                    ExtractPostsJob::dispatch();
                    DispatchAnalysisJob::dispatch();
                    Notification::make()
                        ->success()
                        ->title("Analyzing")
                        ->body("this operation will take a few seconds")
                        ->send();
                })
        ];
    }
}
