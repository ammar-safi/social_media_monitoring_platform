<?php

namespace App\Filament\Resources\ApproveUserResource\Pages;

use App\Filament\Resources\ApproveUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApproveUsers extends ListRecords
{
    protected static string $resource = ApproveUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
