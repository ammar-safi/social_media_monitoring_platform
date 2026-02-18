<?php

namespace App\Filament\Resources\AnalystResource\Pages;

use App\Filament\Resources\AnalystResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnalysts extends ListRecords
{
    protected static string $resource = AnalystResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
