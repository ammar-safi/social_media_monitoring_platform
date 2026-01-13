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
            self::ADMIN => 'pending',
            self::USER => 'user',
            self::POLICY_MAKER => 'policy_maker',
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

}