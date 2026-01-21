<?php

namespace App\Filament\Resources\GovOrgResource\Pages;

use App\Filament\Resources\GovOrgResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGovOrg extends EditRecord
{
    protected static string $resource = GovOrgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
