<?php

namespace App\Filament\Resources\GovOrgResource\Pages;

use App\Filament\Resources\GovOrgResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGovOrg extends ViewRecord
{
    protected static string $resource = GovOrgResource::class;
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make("edit"),
            DeleteAction::make("delete")
        ];
    }
}
