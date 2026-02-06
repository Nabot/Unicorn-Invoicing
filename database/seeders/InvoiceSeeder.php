<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = User::where('email', 'staff@example.com')->first();
        $clients = Client::all();

        if ($clients->isEmpty() || ! $staff) {
            return;
        }

        foreach ($clients->take(2) as $client) {
            // Create a draft invoice
            $draftInvoice = Invoice::create([
                'uuid' => Str::uuid(),
                'invoice_number' => 'INV-' . date('Y') . '-00001',
                'company_id' => 1,
                'client_id' => $client->id,
                'status' => InvoiceStatus::DRAFT,
                'due_date' => now()->addDays(30),
                'subtotal' => 1000.00,
                'vat_total' => 150.00,
                'total' => 1150.00,
                'amount_paid' => 0.00,
                'balance_due' => 1150.00,
                'created_by' => $staff->id,
            ]);

            InvoiceItem::create([
                'invoice_id' => $draftInvoice->id,
                'description' => 'Consulting Services',
                'quantity' => 10,
                'unit_price' => 100.00,
                'vat_applicable' => true,
                'line_subtotal' => 1000.00,
                'line_vat' => 150.00,
                'line_total' => 1150.00,
            ]);

            // Create an issued invoice
            $issuedInvoice = Invoice::create([
                'uuid' => Str::uuid(),
                'invoice_number' => 'INV-' . date('Y') . '-00002',
                'company_id' => 1,
                'client_id' => $client->id,
                'status' => InvoiceStatus::ISSUED,
                'issue_date' => now()->subDays(5),
                'due_date' => now()->addDays(25),
                'subtotal' => 2000.00,
                'vat_total' => 300.00,
                'total' => 2300.00,
                'amount_paid' => 0.00,
                'balance_due' => 2300.00,
                'created_by' => $staff->id,
            ]);

            InvoiceItem::create([
                'invoice_id' => $issuedInvoice->id,
                'description' => 'Software Development',
                'quantity' => 20,
                'unit_price' => 100.00,
                'vat_applicable' => true,
                'line_subtotal' => 2000.00,
                'line_vat' => 300.00,
                'line_total' => 2300.00,
            ]);
        }
    }
}
