<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'invoice_prefix',
        'invoice_format',
        'invoice_number_padding',
        'invoice_reset_yearly',
        'quote_prefix',
        'quote_format',
        'quote_number_padding',
        'quote_reset_yearly',
    ];

    protected function casts(): array
    {
        return [
            'invoice_number_padding' => 'integer',
            'invoice_reset_yearly' => 'boolean',
            'quote_number_padding' => 'integer',
            'quote_reset_yearly' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the settings.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get default settings.
     */
    public static function defaults(): array
    {
        return [
            'invoice_prefix' => 'INV',
            'invoice_format' => '{prefix}-{year}-{number}',
            'invoice_number_padding' => 5,
            'invoice_reset_yearly' => true,
            'quote_prefix' => 'QUO',
            'quote_format' => '{prefix}-{year}-{number}',
            'quote_number_padding' => 5,
            'quote_reset_yearly' => true,
        ];
    }
}
