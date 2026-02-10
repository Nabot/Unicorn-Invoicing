<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-system-settings',
            'view-all-invoices',
            'view-all-reports',
            'manage-clients',
            'create-invoices',
            'edit-invoices',
            'issue-invoices',
            'void-invoices',
            'delete-invoices',
            'record-payments',
            'view-assigned-invoices',
            'view-assigned-clients',
            'view-quotes',
            'create-quotes',
            'edit-quotes',
            'delete-quotes',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $admin = Role::create(['name' => 'Admin']);
        $staff = Role::create(['name' => 'Staff']);
        $agent = Role::create(['name' => 'Agent']);

        // Assign permissions to Admin
        $admin->givePermissionTo([
            'manage-users',
            'manage-system-settings',
            'view-all-invoices',
            'view-all-reports',
            'manage-clients',
            'create-invoices',
            'edit-invoices',
            'issue-invoices',
            'void-invoices',
            'delete-invoices',
            'record-payments',
            'view-quotes',
            'create-quotes',
            'edit-quotes',
            'delete-quotes',
        ]);

        // Assign permissions to Staff
        $staff->givePermissionTo([
            'manage-clients',
            'create-invoices',
            'edit-invoices',
            'issue-invoices',
            'void-invoices',
            'delete-invoices',
            'record-payments',
            'view-assigned-invoices',
            'view-quotes',
            'create-quotes',
            'edit-quotes',
            'delete-quotes',
        ]);

        // Assign permissions to Agent
        $agent->givePermissionTo([
            'view-assigned-invoices',
            'view-assigned-clients',
        ]);
    }
}
