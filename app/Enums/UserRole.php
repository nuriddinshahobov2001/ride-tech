<?php

namespace App\Enums;

enum UserRole: string
{
    case PASSENGER = 'passenger';
    case DRIVER = 'driver';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PASSENGER => 'Пассажир',
            self::DRIVER => 'Водитель',
        };
    }
}
