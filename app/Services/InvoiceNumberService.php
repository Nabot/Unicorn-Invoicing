<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceNumber;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    /**
     * Generate the next sequential invoice number for a company and year.
     */
    public function generateNext(int $companyId, ?int $year = null): string
    {
        $year = $year ?? (int) date('Y');

        return DB::transaction(function () use ($companyId, $year) {
            // Get company settings or use defaults
            $settings = CompanySetting::firstOrCreate(
                ['company_id' => $companyId],
                CompanySetting::defaults()
            );

            $prefix = $settings->invoice_prefix;
            $format = $settings->invoice_format;
            $padding = $settings->invoice_number_padding;
            $resetYearly = $settings->invoice_reset_yearly;

            // Build search pattern based on format
            $searchPattern = $this->buildSearchPattern($format, $prefix, $year, $resetYearly);
            
            // Find the highest existing invoice number for this company
            $existingInvoices = Invoice::where('company_id', $companyId)
                ->where('invoice_number', 'like', $searchPattern)
                ->pluck('invoice_number')
                ->map(function ($number) use ($format, $prefix, $year, $resetYearly) {
                    return $this->extractNumberFromFormat($number, $format, $prefix, $year, $resetYearly);
                })
                ->filter()
                ->values();

            $nextNumber = 1;
            if ($existingInvoices->isNotEmpty()) {
                $nextNumber = $existingInvoices->max() + 1;
            }

            // Get or create the invoice number tracker
            $trackerYear = $resetYearly ? $year : 0; // Use 0 for non-yearly reset
            $invoiceNumber = InvoiceNumber::lockForUpdate()
                ->firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'year' => $trackerYear,
                    ],
                    [
                        'last_number' => 0,
                    ]
                );

            // Ensure the tracker is at least at the next number
            if ($nextNumber > $invoiceNumber->last_number) {
                $invoiceNumber->last_number = $nextNumber - 1;
            }

            // Generate the next number
            $maxAttempts = 10;
            $attempt = 0;
            
            while ($attempt < $maxAttempts) {
                $invoiceNumber->increment('last_number');
                $invoiceNumber->refresh();
                
                $generatedNumber = $this->formatInvoiceNumber(
                    $format,
                    $prefix,
                    $year,
                    $invoiceNumber->last_number,
                    $padding
                );
                
                // Check if this number already exists
                $exists = Invoice::where('invoice_number', $generatedNumber)->exists();
                if (!$exists) {
                    return $generatedNumber;
                }
                
                $attempt++;
            }

            // If we've exhausted attempts, throw an exception
            throw new \Exception("Failed to generate unique invoice number after {$maxAttempts} attempts.");
        });
    }

    /**
     * Format invoice number according to the format string.
     */
    protected function formatInvoiceNumber(string $format, string $prefix, int $year, int $number, int $padding): string
    {
        $numberStr = str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
        
        return str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$prefix, $year, $numberStr],
            $format
        );
    }

    /**
     * Build search pattern for finding existing invoices.
     */
    protected function buildSearchPattern(string $format, string $prefix, int $year, bool $resetYearly): string
    {
        $pattern = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$prefix, $resetYearly ? $year : '%', '%'],
            $format
        );
        
        return str_replace('%', '%', $pattern); // Escape for LIKE query
    }

    /**
     * Extract number from invoice number string based on format.
     */
    protected function extractNumberFromFormat(string $invoiceNumber, string $format, string $prefix, int $year, bool $resetYearly): ?int
    {
        // Convert format to regex pattern
        $regex = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [preg_quote($prefix, '/'), $resetYearly ? $year : '\d+', '(\d+)'],
            $format
        );
        
        $regex = '/^' . $regex . '$/';
        
        if (preg_match($regex, $invoiceNumber, $matches)) {
            return isset($matches[1]) ? (int) $matches[1] : null;
        }
        
        return null;
    }

    /**
     * Get the last invoice number for a company and year.
     */
    public function getLastNumber(int $companyId, ?int $year = null): int
    {
        $year = $year ?? (int) date('Y');
        
        $settings = CompanySetting::where('company_id', $companyId)->first();
        $resetYearly = $settings ? $settings->invoice_reset_yearly : true;
        $trackerYear = $resetYearly ? $year : 0;

        $invoiceNumber = InvoiceNumber::where('company_id', $companyId)
            ->where('year', $trackerYear)
            ->first();

        return $invoiceNumber ? $invoiceNumber->last_number : 0;
    }
}
