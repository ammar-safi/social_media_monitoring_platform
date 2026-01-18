<?php

namespace App\Enums;

enum UserTypeEnum :string 
{
    case ADMIN = 'admin';
    case USER = 'user';
    case POLICY_MAKER = 'policy_maker';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::USER => 'user',
            self::POLICY_MAKER => 'policy maker',
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
            self::ADMIN => 'success',
            self::USER => 'warning',
            self::POLICY_MAKER => 'gray',
        };
    }


}