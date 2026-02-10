<?php

namespace App\Services;

use App\Enums\QuoteStatus;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    public function __construct(
        private QuoteNumberService $quoteNumberService,
        private InvoiceCalculatorService $calculatorService,
        private InvoiceService $invoiceService
    ) {
    }

    /**
     * Create a new draft quote.
     *
     * @param  array<string, mixed>  $quoteData
     * @param  array<int, array<string, mixed>>  $items
     */
    public function createDraft(int $companyId, int $userId, array $quoteData, array $items): Quote
    {
        $maxRetries = 5;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                return DB::transaction(function () use ($companyId, $userId, $quoteData, $items) {
                    // Generate quote number
                    $quoteNumber = $this->quoteNumberService->generateNext($companyId);

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

                    // Create quote
                    $quote = Quote::create([
                        'quote_number' => $quoteNumber,
                        'company_id' => $companyId,
                        'client_id' => $quoteData['client_id'],
                        'status' => QuoteStatus::DRAFT,
                        'quote_date' => $quoteData['quote_date'] ?? now(),
                        'expiry_date' => $quoteData['expiry_date'] ?? null,
                        'subtotal' => $totals['subtotal'],
                        'vat_total' => $totals['vat_total'],
                        'total' => $totals['total'],
                        'notes' => $quoteData['notes'] ?? null,
                        'terms' => $quoteData['terms'] ?? null,
                        'created_by' => $userId,
                    ]);

                    // Create quote items
                    foreach ($items as $index => $item) {
                        QuoteItem::create([
                            'quote_id' => $quote->id,
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

                    return $quote->load('items', 'client');
                });
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed') && str_contains($e->getMessage(), 'quote_number')) {
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw new \Exception("Failed to create quote after {$maxRetries} attempts due to quote number conflicts. Please try again.");
                    }
                    usleep(rand(100000, 500000));
                    continue;
                }
                throw $e;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        throw new \Exception("Failed to create quote after {$maxRetries} attempts.");
    }

    /**
     * Update a quote.
     *
     * @param  array<string, mixed>  $quoteData
     * @param  array<int, array<string, mixed>>  $items
     */
    public function update(Quote $quote, array $quoteData, array $items): Quote
    {
        if (! $quote->canBeEdited()) {
            throw new \Exception('Quote cannot be edited in its current status.');
        }

        return DB::transaction(function () use ($quote, $quoteData, $items) {
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

            // Update quote
            $quote->update([
                'client_id' => $quoteData['client_id'] ?? $quote->client_id,
                'quote_date' => $quoteData['quote_date'] ?? $quote->quote_date,
                'expiry_date' => $quoteData['expiry_date'] ?? $quote->expiry_date,
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat_total'],
                'total' => $totals['total'],
                'notes' => $quoteData['notes'] ?? $quote->notes,
                'terms' => $quoteData['terms'] ?? $quote->terms,
            ]);

            // Delete existing items and create new ones
            $quote->items()->delete();
            foreach ($items as $index => $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
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

            return $quote->load('items', 'client');
        });
    }

    /**
     * Send a quote (change status to sent).
     */
    public function send(Quote $quote): Quote
    {
        if ($quote->status !== QuoteStatus::DRAFT) {
            throw new \Exception('Only draft quotes can be sent.');
        }

        $quote->update([
            'status' => QuoteStatus::SENT,
        ]);

        return $quote->load('items', 'client');
    }

    /**
     * Accept a quote (change status to accepted).
     */
    public function accept(Quote $quote): Quote
    {
        if ($quote->status !== QuoteStatus::SENT) {
            throw new \Exception('Only sent quotes can be accepted.');
        }

        $quote->update([
            'status' => QuoteStatus::ACCEPTED,
        ]);

        return $quote->load('items', 'client');
    }

    /**
     * Reject a quote (change status to rejected).
     */
    public function reject(Quote $quote): Quote
    {
        if ($quote->status !== QuoteStatus::SENT) {
            throw new \Exception('Only sent quotes can be rejected.');
        }

        $quote->update([
            'status' => QuoteStatus::REJECTED,
        ]);

        return $quote->load('items', 'client');
    }

    /**
     * Convert an accepted quote to an invoice.
     *
     * @param  array<string, mixed>  $invoiceData Additional data for invoice (e.g., due_date)
     */
    public function convertToInvoice(Quote $quote, array $invoiceData = []): Invoice
    {
        if (!$quote->canConvertToInvoice()) {
            throw new \Exception('Only accepted quotes that have not been converted can be converted to invoices.');
        }

        return DB::transaction(function () use ($quote, $invoiceData) {
            // Create invoice from quote data
            $invoice = $this->invoiceService->createDraft(
                $quote->company_id,
                $quote->created_by,
                [
                    'client_id' => $quote->client_id,
                    'due_date' => $invoiceData['due_date'] ?? now()->addDays(30),
                    'notes' => $quote->notes,
                    'terms' => $quote->terms,
                ],
                $quote->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'discount' => $item->discount,
                        'vat_applicable' => $item->vat_applicable,
                    ];
                })->toArray()
            );

            // Link quote to invoice
            $quote->update([
                'invoice_id' => $invoice->id,
            ]);

            return $invoice->load('items', 'client');
        });
    }
}
