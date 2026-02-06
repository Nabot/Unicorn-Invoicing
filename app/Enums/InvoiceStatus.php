<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case VOID = 'void';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ISSUED => 'Issued',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::PAID => 'Paid',
            self::VOID => 'Void',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::ISSUED => 'blue',
            self::PARTIALLY_PAID => 'yellow',
            self::PAID => 'green',
            self::VOID => 'red',
        };
    }

    public function canEdit(): bool
    {
        return match ($this) {
            self::DRAFT => true,
            self::ISSUED => true,
            self::PARTIALLY_PAID => false,
            self::PAID => false,
            self::VOID => false,
        };
    }
}
