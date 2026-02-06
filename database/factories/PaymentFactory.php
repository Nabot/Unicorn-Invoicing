<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'invoice_id' => Invoice::factory(),
            'amount' => fake()->randomFloat(2, 100, 1000),
            'payment_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'method' => fake()->randomElement(PaymentMethod::cases()),
            'reference' => fake()->optional()->numerify('REF#######'),
            'created_by' => User::factory(),
        ];
    }
}
