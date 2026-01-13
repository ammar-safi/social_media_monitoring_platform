<?php

namespace App\Enums;

enum AnalystSentimentEnum :string 
{
    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';

    public function label(): string
    {
        return match ($this) {
            self::POSITIVE => 'positive',
            self::NEGATIVE => 'negative',
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
            self::NEGATIVE => 'rejected',
        };
    }
    
    public function icon(): string
    {
        return match ($this) {
            self::POSITIVE => 'heroicon-o-clock',
            self::NEGATIVE => 'heroicon-o-check-circle',
        };
    }
}