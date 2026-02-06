<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Staff']);
        $this->user = User::factory()->create(['company_id' => 1]);
        $this->user->assignRole('Staff');
        $this->user->givePermissionTo(['create-invoices', 'edit-invoices', 'issue-invoices']);

        $this->client = Client::factory()->create(['company_id' => 1]);
    }

    public function test_staff_can_create_draft_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'client_id' => $this->client->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                [
                    'description' => 'Test Item',
                    'quantity' => 10,
                    'unit_price' => 100,
                    'vat_applicable' => true,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'status' => InvoiceStatus::DRAFT->value,
        ]);

        $invoice = Invoice::where('client_id', $this->client->id)->first();
        $this->assertEquals(1000.00, $invoice->subtotal);
        $this->assertEquals(150.00, $invoice->vat_total);
        $this->assertEquals(1150.00, $invoice->total);
    }

    public function test_staff_can_issue_draft_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => 1,
            'client_id' => $this->client->id,
            'status' => InvoiceStatus::DRAFT,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('invoices.issue', $invoice));

        $response->assertRedirect();
        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::ISSUED, $invoice->status);
        $this->assertNotNull($invoice->issue_date);
    }

    public function test_cannot_issue_non_draft_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => 1,
            'client_id' => $this->client->id,
            'status' => InvoiceStatus::ISSUED,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('invoices.issue', $invoice));

        $response->assertSessionHas('error');
    }

    public function test_invoice_calculates_totals_correctly(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'client_id' => $this->client->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                [
                    'description' => 'Item 1',
                    'quantity' => 2,
                    'unit_price' => 100,
                    'vat_applicable' => true,
                ],
                [
                    'description' => 'Item 2',
                    'quantity' => 1,
                    'unit_price' => 200,
                    'vat_applicable' => false,
                ],
            ],
        ]);

        $invoice = Invoice::latest()->first();
        $this->assertEquals(400.00, $invoice->subtotal); // 200 + 200
        $this->assertEquals(30.00, $invoice->vat_total); // 200 * 0.15
        $this->assertEquals(430.00, $invoice->total); // 400 + 30
    }
}
