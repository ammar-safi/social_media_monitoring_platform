<?php

namespace App\Enums;

enum AnalystStanceEnum :string 
{
    case SUPPORTIVE = 'supportive';
    case AGAINST = 'against';

    public function label(): string
    {
        return match ($this) {
            self::SUPPORTIVE => 'supportive',
            self::AGAINST => 'against',
        };
    }
    public static function asSelectArray(): array
    {
        return array_reduce(
            self::cases(),
            fn($carry, $case) => $carry + [$case->value => $case->label()],
            []
        );
    }


    public function badgeColor(): string
    {
        return match ($this) {
            self::SUPPORTIVE => 'success',
            self::AGAINST => 'danger',
        };
    }
    
    public function icon(): string
    {
        return match ($this) {
            self::SUPPORTIVE => 'heroicon-o-face-smile',
            self::AGAINST => 'heroicon-o-arrow-face-frown',
        };
    }
}