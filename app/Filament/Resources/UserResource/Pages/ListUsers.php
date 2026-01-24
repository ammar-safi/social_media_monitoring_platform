<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserTypeEnum;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all users' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query
                        ->where("active", 1);
                }),
            'Government official' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query
                        ->where("type", UserTypeEnum::USER->value)
                        ->where("active", 1)
                    ;
                }),
            'Policy maker' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query->where("type", UserTypeEnum::POLICY_MAKER->value)
                        ->where("active", 1)
                    ;
                }),
            'Not active' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query->where("active", 0);
                }),

        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
