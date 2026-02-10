<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Quote;
use App\Models\QuoteNumber;
use Illuminate\Support\Facades\DB;

class QuoteNumberService
{
    /**
     * Generate the next sequential quote number for a company and year.
     */
    public function generateNext(int $companyId, ?int $year = null): string
    {
        $year = $year ?? (int) date('Y');

        return DB::transaction(function () use ($companyId, $year) {
            // Ensure company exists before creating settings
            $company = \App\Models\Company::find($companyId);
            if (!$company) {
                throw new \Exception("Company with ID {$companyId} does not exist. Please ensure the company is created first.");
            }
            
            // Get company settings or use defaults
            $settings = CompanySetting::firstOrCreate(
                ['company_id' => $companyId],
                CompanySetting::defaults()
            );

            $prefix = $settings->quote_prefix ?? 'QUO';
            $format = $settings->quote_format ?? '{prefix}-{year}-{number}';
            $padding = $settings->quote_number_padding ?? 5;
            $resetYearly = $settings->quote_reset_yearly ?? true;

            // Build search pattern based on format
            $searchPattern = $this->buildSearchPattern($format, $prefix, $year, $resetYearly);
            
            // Find the highest existing quote number for this company
            $existingQuotes = Quote::where('company_id', $companyId)
                ->where('quote_number', 'like', $searchPattern)
                ->pluck('quote_number')
                ->map(function ($number) use ($format, $prefix, $year, $resetYearly) {
                    return $this->extractNumberFromFormat($number, $format, $prefix, $year, $resetYearly);
                })
                ->filter()
                ->values();

            $nextNumber = 1;
            if ($existingQuotes->isNotEmpty()) {
                $nextNumber = $existingQuotes->max() + 1;
            }

            // Get or create the quote number tracker
            $trackerYear = $resetYearly ? $year : 0;
            $quoteNumber = QuoteNumber::lockForUpdate()
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
            if ($nextNumber > $quoteNumber->last_number) {
                $quoteNumber->last_number = $nextNumber - 1;
            }

            // Generate the next number
            $maxAttempts = 10;
            $attempt = 0;
            
            while ($attempt < $maxAttempts) {
                $quoteNumber->increment('last_number');
                $quoteNumber->refresh();
                
                $generatedNumber = $this->formatQuoteNumber(
                    $format,
                    $prefix,
                    $year,
                    $quoteNumber->last_number,
                    $padding
                );
                
                // Check if this number already exists
                $exists = Quote::where('quote_number', $generatedNumber)->exists();
                if (!$exists) {
                    return $generatedNumber;
                }
                
                $attempt++;
            }

            throw new \Exception("Failed to generate unique quote number after {$maxAttempts} attempts.");
        });
    }

    /**
     * Format quote number according to the format string.
     */
    protected function formatQuoteNumber(string $format, string $prefix, int $year, int $number, int $padding): string
    {
        $numberStr = str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
        
        return str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$prefix, $year, $numberStr],
            $format
        );
    }

    /**
     * Build search pattern for finding existing quotes.
     */
    protected function buildSearchPattern(string $format, string $prefix, int $year, bool $resetYearly): string
    {
        $pattern = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [$prefix, $resetYearly ? $year : '%', '%'],
            $format
        );
        
        return str_replace('%', '%', $pattern);
    }

    /**
     * Extract number from quote number string based on format.
     */
    protected function extractNumberFromFormat(string $quoteNumber, string $format, string $prefix, int $year, bool $resetYearly): ?int
    {
        $regex = str_replace(
            ['{prefix}', '{year}', '{number}'],
            [preg_quote($prefix, '/'), $resetYearly ? $year : '\d+', '(\d+)'],
            $format
        );
        
        $regex = '/^' . $regex . '$/';
        
        if (preg_match($regex, $quoteNumber, $matches)) {
            return isset($matches[1]) ? (int) $matches[1] : null;
        }
        
        return null;
    }
}
