<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case EFT = 'eft';
    case CARD = 'card';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::EFT => 'EFT',
            self::CARD => 'Card',
            self::OTHER => 'Other',
        };
    }
}
