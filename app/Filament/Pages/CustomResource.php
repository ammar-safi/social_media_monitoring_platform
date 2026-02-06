<?php

namespace App\Filament\Pages;

use App\Filament\Resources\UserResource;
use Carbon\Carbon;
use Closure;
use Filament\Infolists\Components\Actions\Action as ActionsAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;

class CustomResource extends Resource
{
    public static function getDateFormattedColumn(String $column)
    {
        return
            TextColumn::make($column)
            ->formatStateUsing(function ($state) {
                $date = Carbon::parse($state);
                $readable_date = $date->diffForHumans();
                $state = Carbon::parse($state)->format("d/M/Y");
                return $state . " (" .  $readable_date . ")";
            })
        ;
    }

    public static function getStatusColumn(String $column)
    {
        return
            TextColumn::make($column)
            ->badge()
            ->formatStateUsing(function ($state) {
                return $state->label();
            })
            ->color(function ($state): string {
                return $state->badgeColor();
            })
        ;
    }



    public static function getDateFormattedEntry(String $column)
    {
        return
            TextEntry::make($column)
            ->formatStateUsing(function ($state) {
                $date = Carbon::parse($state);
                $readable_date = $date->diffForHumans();
                $state = Carbon::parse($state)->format("d/M/Y");
                return $state . " (" .  $readable_date . ")";
            })
        ;
    }
    public static function getStatusEntry(String $column)
    {
        return
            TextEntry::make($column)
            ->badge()
            ->formatStateUsing(function ($state) {
                return $state->label();
            })
            ->color(function ($state): string {
                return $state->badgeColor();
            });
    }

    
}
