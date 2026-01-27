<?php

namespace App\Filament\Resources\InviteResource\Pages;

use App\Filament\Pages\InvitePolicyMaker;
use App\Filament\Resources\InviteResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListInvites extends ListRecords
{
    protected static string $resource = InviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("create")
                ->label("Invite Policy maker")
                ->url(InvitePolicyMaker::getUrl())
        ];
    }
}
