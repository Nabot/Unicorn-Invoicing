<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'company_id' => 1,
            'uuid' => Str::uuid(),
        ]);
        $admin->assignRole('Admin');

        // Create Staff user
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'company_id' => 1,
            'uuid' => Str::uuid(),
        ]);
        $staff->assignRole('Staff');

        // Create Agent user
        $agent = User::create([
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'password' => Hash::make('password'),
            'company_id' => 1,
            'uuid' => Str::uuid(),
        ]);
        $agent->assignRole('Agent');
    }
}
