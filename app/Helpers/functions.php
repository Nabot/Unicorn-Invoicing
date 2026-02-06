<?php

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency.
     */
    function format_currency($amount, $currency = null): string
    {
        $currency = $currency ?? config('app.currency', 'R');
        return $currency . number_format((float) $amount, 2);
    }
}

if (!function_exists('currency')) {
    /**
     * Format a number as currency (alias for format_currency).
     */
    function currency(float $amount, ?string $currency = null): string
    {
        return format_currency($amount, $currency);
    }
}

if (!function_exists('format_money')) {
    /**
     * Format a number as currency (alias for format_currency).
     */
    function format_money(float $amount, ?string $currency = null): string
    {
        return format_currency($amount, $currency);
    }
}
