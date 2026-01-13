<?php

namespace App\Enums;

enum AnalystSentimentEnum :string 
{
    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';
    case NORMAL = 'normal';

    public function label(): string
    {
        return match ($this) {
            self::POSITIVE => 'positive',
            self::NEGATIVE => 'negative',
            self::NORMAL => 'normal',
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
            self::POSITIVE => 'success',
            self::NEGATIVE => 'danger',
            self::NORMAL => 'normal',
        };
    }
    
    public function icon(): string
    {
        return match ($this) {
            self::POSITIVE => 'heroicon-o-face-smile',
            self::NEGATIVE => 'heroicon-o-face-frown',
            self::NORMAL => 'heroicon-o-face-meh',
        };
    }
}