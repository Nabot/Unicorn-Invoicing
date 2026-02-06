<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'invoice_number' => 'INV-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'company_id' => 1,
            'client_id' => Client::factory(),
            'status' => InvoiceStatus::DRAFT,
            'issue_date' => null,
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'subtotal' => 1000.00,
            'vat_total' => 150.00,
            'total' => 1150.00,
            'amount_paid' => 0.00,
            'balance_due' => 1150.00,
            'notes' => fake()->optional()->sentence(),
            'terms' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
