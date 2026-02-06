<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format a number as currency.
     */
    public static function format(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? config('app.currency', 'R');
        return $currency . ' ' . number_format($amount, 2);
    }

    /**
     * Format a number as currency without symbol (for display where symbol is shown separately).
     */
    public static function formatAmount(float $amount): string
    {
        return number_format($amount, 2);
    }
}
