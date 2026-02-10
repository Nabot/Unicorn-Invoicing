<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create delete-invoices permission
        $permission = Permission::firstOrCreate(['name' => 'delete-invoices']);

        // Assign permission to Admin role
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permission);
        }

        // Assign permission to Staff role
        $staff = Role::where('name', 'Staff')->first();
        if ($staff) {
            $staff->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permission from roles
        $admin = Role::where('name', 'Admin')->first();
        $staff = Role::where('name', 'Staff')->first();

        $permission = Permission::where('name', 'delete-invoices')->first();

        if ($admin && $permission) {
            $admin->revokePermissionTo($permission);
        }

        if ($staff && $permission) {
            $staff->revokePermissionTo($permission);
        }

        // Delete permission
        if ($permission) {
            $permission->delete();
        }
    }
};
