<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Acme Corporation',
                'email' => 'contact@acme.com',
                'phone' => '+1-555-0101',
                'address' => '123 Business St, City, State 12345',
                'vat_number' => 'VAT123456',
            ],
            [
                'name' => 'Tech Solutions Ltd',
                'email' => 'info@techsolutions.com',
                'phone' => '+1-555-0102',
                'address' => '456 Innovation Ave, City, State 12345',
                'vat_number' => 'VAT789012',
            ],
            [
                'name' => 'Global Services Inc',
                'email' => 'hello@globalservices.com',
                'phone' => '+1-555-0103',
                'address' => '789 Enterprise Blvd, City, State 12345',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create([
                'uuid' => Str::uuid(),
                'company_id' => 1,
                ...$clientData,
            ]);
        }
    }
}
