<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Events\InvoiceCreated;
use App\Events\InvoiceIssued;
use App\Events\InvoiceUpdated;
use App\Events\InvoiceVoided;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private InvoiceNumberService $invoiceNumberService,
        private InvoiceCalculatorService $calculatorService
    ) {
    }

    /**
     * Create a new draft invoice.
     *
     * @param  array<string, mixed>  $invoiceData
     * @param  array<int, array<string, mixed>>  $items
     */
    public function createDraft(int $companyId, int $userId, array $invoiceData, array $items): Invoice
    {
        $maxRetries = 5;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                return DB::transaction(function () use ($companyId, $userId, $invoiceData, $items) {
                    // Generate invoice number (this already checks for uniqueness)
                    $invoiceNumber = $this->invoiceNumberService->generateNext($companyId);

                    // Calculate totals
                    $calculatedItems = [];
                    foreach ($items as $item) {
                        $calculatedItems[] = $this->calculatorService->calculateLineItem(
                            (float) $item['quantity'],
                            (float) $item['unit_price'],
                            (bool) ($item['vat_applicable'] ?? true),
                            (float) ($item['discount'] ?? 0.00)
                        );
                    }

                    $totals = $this->calculatorService->calculateInvoiceTotals($calculatedItems);

                    // Create invoice
                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'company_id' => $companyId,
                        'client_id' => $invoiceData['client_id'],
                        'status' => InvoiceStatus::DRAFT,
                        'due_date' => $invoiceData['due_date'],
                        'subtotal' => $totals['subtotal'],
                        'vat_total' => $totals['vat_total'],
                        'total' => $totals['total'],
                        'amount_paid' => 0.00,
                        'balance_due' => $totals['total'],
                        'notes' => $invoiceData['notes'] ?? null,
                        'terms' => $invoiceData['terms'] ?? null,
                        'created_by' => $userId,
                    ]);

                    // Create invoice items
                    foreach ($items as $index => $item) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'discount' => $item['discount'] ?? 0.00,
                            'vat_applicable' => $item['vat_applicable'] ?? true,
                            'line_subtotal' => $calculatedItems[$index]['line_subtotal'],
                            'line_vat' => $calculatedItems[$index]['line_vat'],
                            'line_total' => $calculatedItems[$index]['line_total'],
                        ]);
                    }

                    event(new InvoiceCreated($invoice));

                    return $invoice->load('items', 'client');
                });
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a unique constraint violation on invoice_number
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed') && str_contains($e->getMessage(), 'invoice_number')) {
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw new \Exception("Failed to create invoice after {$maxRetries} attempts due to invoice number conflicts. Please try again.");
                    }
                    // Wait a small random amount to avoid simultaneous retries
                    usleep(rand(100000, 500000)); // 100-500ms
                    continue;
                }
                throw $e;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        throw new \Exception("Failed to create invoice after {$maxRetries} attempts.");
    }

    /**
     * Update an invoice.
     *
     * @param  array<string, mixed>  $invoiceData
     * @param  array<int, array<string, mixed>>  $items
     */
    public function update(Invoice $invoice, array $invoiceData, array $items): Invoice
    {
        if (! $invoice->canBeEdited()) {
            throw new \Exception('Invoice cannot be edited in its current status.');
        }

        return DB::transaction(function () use ($invoice, $invoiceData, $items) {
            // Calculate totals
            $calculatedItems = [];
            foreach ($items as $item) {
                $calculatedItems[] = $this->calculatorService->calculateLineItem(
                    (float) $item['quantity'],
                    (float) $item['unit_price'],
                    (bool) ($item['vat_applicable'] ?? true),
                    (float) ($item['discount'] ?? 0.00)
                );
            }

            $totals = $this->calculatorService->calculateInvoiceTotals($calculatedItems);

            // Update invoice
            $invoice->update([
                'client_id' => $invoiceData['client_id'] ?? $invoice->client_id,
                'due_date' => $invoiceData['due_date'] ?? $invoice->due_date,
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat_total'],
                'total' => $totals['total'],
                'balance_due' => $this->calculatorService->calculateBalanceDue(
                    $totals['total'],
                    $invoice->amount_paid
                ),
                'notes' => $invoiceData['notes'] ?? $invoice->notes,
                'terms' => $invoiceData['terms'] ?? $invoice->terms,
            ]);

            // Delete existing items and create new ones
            $invoice->items()->delete();
            foreach ($items as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0.00,
                    'vat_applicable' => $item['vat_applicable'] ?? true,
                    'line_subtotal' => $calculatedItems[$index]['line_subtotal'],
                    'line_vat' => $calculatedItems[$index]['line_vat'],
                    'line_total' => $calculatedItems[$index]['line_total'],
                ]);
            }

            event(new InvoiceUpdated($invoice));

            return $invoice->load('items', 'client');
        });
    }

    /**
     * Issue an invoice.
     */
    public function issue(Invoice $invoice): Invoice
    {
        if ($invoice->status !== InvoiceStatus::DRAFT) {
            throw new \Exception('Only draft invoices can be issued.');
        }

        $invoice->update([
            'status' => InvoiceStatus::ISSUED,
            'issue_date' => now(),
        ]);

        event(new InvoiceIssued($invoice));

        return $invoice->load('items', 'client');
    }

    /**
     * Void an invoice.
     */
    public function void(Invoice $invoice): Invoice
    {
        if ($invoice->status === InvoiceStatus::VOID) {
            throw new \Exception('Invoice is already void.');
        }

        if ($invoice->status === InvoiceStatus::PAID) {
            throw new \Exception('Paid invoices cannot be voided.');
        }

        $invoice->update([
            'status' => InvoiceStatus::VOID,
        ]);

        event(new InvoiceVoided($invoice));

        return $invoice->load('items', 'client');
    }
}
