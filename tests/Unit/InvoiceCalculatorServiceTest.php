<?php

namespace Tests\Unit;

use App\Services\InvoiceCalculatorService;
use PHPUnit\Framework\TestCase;

class InvoiceCalculatorServiceTest extends TestCase
{
    private InvoiceCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new InvoiceCalculatorService();
    }

    public function test_calculates_line_item_without_vat(): void
    {
        $result = $this->calculator->calculateLineItem(10, 100, false);

        $this->assertEquals(1000.00, $result['line_subtotal']);
        $this->assertEquals(0.00, $result['line_vat']);
        $this->assertEquals(1000.00, $result['line_total']);
    }

    public function test_calculates_line_item_with_vat(): void
    {
        $result = $this->calculator->calculateLineItem(10, 100, true);

        $this->assertEquals(1000.00, $result['line_subtotal']);
        $this->assertEquals(150.00, $result['line_vat']);
        $this->assertEquals(1150.00, $result['line_total']);
    }

    public function test_calculates_line_item_with_decimal_quantities(): void
    {
        $result = $this->calculator->calculateLineItem(2.5, 100, true);

        $this->assertEquals(250.00, $result['line_subtotal']);
        $this->assertEquals(37.50, $result['line_vat']);
        $this->assertEquals(287.50, $result['line_total']);
    }

    public function test_calculates_invoice_totals_from_items(): void
    {
        $items = [
            ['line_subtotal' => 1000.00, 'line_vat' => 150.00, 'line_total' => 1150.00],
            ['line_subtotal' => 500.00, 'line_vat' => 0.00, 'line_total' => 500.00],
            ['line_subtotal' => 200.00, 'line_vat' => 30.00, 'line_total' => 230.00],
        ];

        $totals = $this->calculator->calculateInvoiceTotals($items);

        $this->assertEquals(1700.00, $totals['subtotal']);
        $this->assertEquals(180.00, $totals['vat_total']);
        $this->assertEquals(1880.00, $totals['total']);
    }

    public function test_calculates_balance_due(): void
    {
        $balance = $this->calculator->calculateBalanceDue(1000.00, 300.00);

        $this->assertEquals(700.00, $balance);
    }

    public function test_balance_due_never_negative(): void
    {
        $balance = $this->calculator->calculateBalanceDue(1000.00, 1500.00);

        $this->assertEquals(0.00, $balance);
    }

    public function test_rounding_consistency(): void
    {
        // Test edge case with rounding
        $result = $this->calculator->calculateLineItem(1, 33.33, true);

        // Should round to 2 decimals
        $this->assertEquals(33.33, $result['line_subtotal']);
        $this->assertEquals(5.00, $result['line_vat']); // 33.33 * 0.15 = 4.9995, rounded to 5.00
        $this->assertEquals(38.33, $result['line_total']);
    }

    public function test_returns_vat_rate(): void
    {
        $this->assertEquals(0.15, $this->calculator->getVatRate());
    }
}
