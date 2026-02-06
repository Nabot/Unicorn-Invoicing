<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Staff']);
        $this->user = User::factory()->create(['company_id' => 1]);
        $this->user->assignRole('Staff');
        $this->user->givePermissionTo('record-payments');

        $client = Client::factory()->create(['company_id' => 1]);
        $this->invoice = Invoice::factory()->create([
            'company_id' => 1,
            'client_id' => $client->id,
            'status' => InvoiceStatus::ISSUED,
            'total' => 1000.00,
            'balance_due' => 1000.00,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_staff_can_record_payment(): void
    {
        $response = $this->actingAs($this->user)->post(route('payments.store', $this->invoice), [
            'amount' => 500.00,
            'payment_date' => now()->format('Y-m-d'),
            'method' => PaymentMethod::EFT->value,
            'reference' => 'REF123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
        ]);

        $this->invoice->refresh();
        $this->assertEquals(500.00, $this->invoice->amount_paid);
        $this->assertEquals(500.00, $this->invoice->balance_due);
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID, $this->invoice->status);
    }

    public function test_payment_updates_invoice_to_paid_when_full(): void
    {
        $response = $this->actingAs($this->user)->post(route('payments.store', $this->invoice), [
            'amount' => 1000.00,
            'payment_date' => now()->format('Y-m-d'),
            'method' => PaymentMethod::EFT->value,
        ]);

        $response->assertRedirect();
        $this->invoice->refresh();
        $this->assertEquals(InvoiceStatus::PAID, $this->invoice->status);
        $this->assertEquals(0.00, $this->invoice->balance_due);
    }

    public function test_cannot_record_payment_exceeding_balance(): void
    {
        $response = $this->actingAs($this->user)->post(route('payments.store', $this->invoice), [
            'amount' => 1500.00,
            'payment_date' => now()->format('Y-m-d'),
            'method' => PaymentMethod::EFT->value,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_cannot_record_payment_for_void_invoice(): void
    {
        $this->invoice->update(['status' => InvoiceStatus::VOID]);

        $response = $this->actingAs($this->user)->post(route('payments.store', $this->invoice), [
            'amount' => 500.00,
            'payment_date' => now()->format('Y-m-d'),
            'method' => PaymentMethod::EFT->value,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_can_delete_payment(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'created_by' => $this->user->id,
        ]);

        $this->invoice->update([
            'amount_paid' => 500.00,
            'balance_due' => 500.00,
            'status' => InvoiceStatus::PARTIALLY_PAID,
        ]);

        $response = $this->actingAs($this->user)->delete(route('payments.destroy', [$this->invoice, $payment]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);

        $this->invoice->refresh();
        $this->assertEquals(0.00, $this->invoice->amount_paid);
        $this->assertEquals(1000.00, $this->invoice->balance_due);
    }
}
