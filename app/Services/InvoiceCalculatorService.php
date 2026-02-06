<?php

namespace App\Services;

class InvoiceCalculatorService
{
    private const VAT_RATE = 0.15;

    /**
     * Calculate line item totals.
     *
     * @return array{line_subtotal: float, line_vat: float, line_total: float}
     */
    public function calculateLineItem(float $quantity, float $unitPrice, bool $vatApplicable, float $discount = 0.00): array
    {
        $lineSubtotal = round($quantity * $unitPrice, 2);
        $discountAmount = round($discount, 2);
        $lineSubtotalAfterDiscount = round($lineSubtotal - $discountAmount, 2);
        $lineVat = $vatApplicable ? round($lineSubtotalAfterDiscount * self::VAT_RATE, 2) : 0.00;
        $lineTotal = round($lineSubtotalAfterDiscount + $lineVat, 2);

        return [
            'line_subtotal' => $lineSubtotalAfterDiscount,
            'line_vat' => $lineVat,
            'line_total' => $lineTotal,
        ];
    }

    /**
     * Calculate invoice totals from items.
     *
     * @param  array<int, array{line_subtotal: float, line_vat: float, line_total: float}>  $items
     * @return array{subtotal: float, vat_total: float, total: float}
     */
    public function calculateInvoiceTotals(array $items): array
    {
        $subtotal = 0.00;
        $vatTotal = 0.00;
        $total = 0.00;

        foreach ($items as $item) {
            $subtotal += $item['line_subtotal'];
            $vatTotal += $item['line_vat'];
            $total += $item['line_total'];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'vat_total' => round($vatTotal, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Calculate balance due after payments.
     */
    public function calculateBalanceDue(float $total, float $amountPaid): float
    {
        return round(max(0, $total - $amountPaid), 2);
    }

    /**
     * Get the VAT rate.
     */
    public function getVatRate(): float
    {
        return self::VAT_RATE;
    }
}
