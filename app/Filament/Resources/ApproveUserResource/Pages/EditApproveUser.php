<?php

namespace App\Filament\Resources\ApproveUserResource\Pages;

use App\Filament\Resources\ApproveUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApproveUser extends EditRecord
{
    protected static string $resource = ApproveUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
