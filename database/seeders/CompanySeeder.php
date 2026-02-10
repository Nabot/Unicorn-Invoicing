<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default company
        Company::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Unicorn Supplies CC',
                'email' => 'supply@unicorn.com.na',
                'phone' => '+264811600014',
                'address' => null,
                'tax_id' => '11070239',
            ]
        );
    }
}
