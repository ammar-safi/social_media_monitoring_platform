<?php

namespace App\Filament\Resources\InviteResource\Pages;

use App\Enums\UserTypeEnum;
use App\Filament\Pages\InvitePolicyMaker;
use App\Filament\Resources\InviteResource;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListInvites extends ListRecords
{
    protected static string $resource = InviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("invite")
                ->label("Invite Policy maker")
                ->url(InvitePolicyMaker::getUrl()),
            Action::make("create")
                ->color("gray")
                ->label("create Policy maker")
                ->url(UserResource::getUrl("create", ["type" => UserTypeEnum::POLICY_MAKER->value]))
        ];
    }
}
