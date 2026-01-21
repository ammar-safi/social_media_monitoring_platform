<?php

namespace App\Filament\Resources\GovOrgResource\Pages;

use App\Filament\Resources\GovOrgResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGovOrgs extends ListRecords
{
    protected static string $resource = GovOrgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
