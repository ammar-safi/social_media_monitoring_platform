<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;

class CustomResource extends Resource
{
    public static function getDateFormattedColumn($column)
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

    public static function getStatusColumn($column)
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



    public static function getDateFormattedEntry($column)
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
    public static function getStatusEntry($column)
    {
        return
            TextEntry::make($column)
            ->badge()
            ->formatStateUsing(function ($state) {
                return $state->label();
            })
            ->color(function ($state): string {
                return $state->badgeColor();
            })
        ;
    }
}
