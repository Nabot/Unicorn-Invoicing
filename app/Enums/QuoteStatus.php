<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
            self::EXPIRED => 'orange',
        };
    }

    public function canEdit(): bool
    {
        return match ($this) {
            self::DRAFT => true,
            self::SENT => true,
            self::ACCEPTED => false,
            self::REJECTED => false,
            self::EXPIRED => false,
        };
    }

    public function canConvertToInvoice(): bool
    {
        return $this === self::ACCEPTED;
    }
}
