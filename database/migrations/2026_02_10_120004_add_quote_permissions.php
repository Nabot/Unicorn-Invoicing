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

        // Create quote permissions
        $permissions = [
            'view-quotes',
            'create-quotes',
            'edit-quotes',
            'delete-quotes',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign permissions to Admin role
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        // Assign permissions to Staff role
        $staff = Role::where('name', 'Staff')->first();
        if ($staff) {
            $staff->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions from roles
        $admin = Role::where('name', 'Admin')->first();
        $staff = Role::where('name', 'Staff')->first();

        $permissions = ['view-quotes', 'create-quotes', 'edit-quotes', 'delete-quotes'];

        if ($admin) {
            $admin->revokePermissionTo($permissions);
        }

        if ($staff) {
            $staff->revokePermissionTo($permissions);
        }

        // Delete permissions
        foreach ($permissions as $permissionName) {
            Permission::where('name', $permissionName)->delete();
        }
    }
};
