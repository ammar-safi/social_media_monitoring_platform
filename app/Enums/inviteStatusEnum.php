<?php

namespace App\Enums;

enum InviteStatusEnum :string 
{
    case PENDING = 'pending' ;
    case USED = 'used';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::USED => 'used',
            self::EXPIRED => 'expired',
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
            self::PENDING => 'gray',
            self::USED => 'success',
            self::EXPIRED => 'danger',
        };
    }
    
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::USED => 'heroicon-o-check-circle',
            self::EXPIRED => 'heroicon-o-x-circle',
        };
    }
}