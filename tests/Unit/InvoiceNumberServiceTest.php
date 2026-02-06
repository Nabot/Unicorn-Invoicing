<?php

namespace Tests\Unit;

use App\Models\InvoiceNumber;
use App\Services\InvoiceNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceNumberService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoiceNumberService();
    }

    public function test_generates_sequential_invoice_numbers(): void
    {
        $companyId = 1;
        $year = 2026;

        $number1 = $this->service->generateNext($companyId, $year);
        $number2 = $this->service->generateNext($companyId, $year);
        $number3 = $this->service->generateNext($companyId, $year);

        $this->assertEquals('INV-2026-00001', $number1);
        $this->assertEquals('INV-2026-00002', $number2);
        $this->assertEquals('INV-2026-00003', $number3);
    }

    public function test_generates_numbers_per_company(): void
    {
        $year = 2026;

        $number1 = $this->service->generateNext(1, $year);
        $number2 = $this->service->generateNext(2, $year);

        $this->assertEquals('INV-2026-00001', $number1);
        $this->assertEquals('INV-2026-00001', $number2); // Different company, starts at 1
    }

    public function test_generates_numbers_per_year(): void
    {
        $companyId = 1;

        $number1 = $this->service->generateNext($companyId, 2025);
        $number2 = $this->service->generateNext($companyId, 2026);

        $this->assertEquals('INV-2025-00001', $number1);
        $this->assertEquals('INV-2026-00001', $number2); // Different year, starts at 1
    }

    public function test_uses_current_year_when_not_specified(): void
    {
        $companyId = 1;
        $currentYear = (int) date('Y');

        $number = $this->service->generateNext($companyId);

        $this->assertStringStartsWith("INV-{$currentYear}-", $number);
    }

    public function test_gets_last_number(): void
    {
        $companyId = 1;
        $year = 2026;

        $this->assertEquals(0, $this->service->getLastNumber($companyId, $year));

        $this->service->generateNext($companyId, $year);
        $this->service->generateNext($companyId, $year);

        $this->assertEquals(2, $this->service->getLastNumber($companyId, $year));
    }
}
