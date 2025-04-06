<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'manage_accounts',
            'view_reports',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
            'approve_transactions'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'superadmin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view_dashboard',
            'manage_users',
            'view_reports',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
            'approve_transactions'
        ]);

        $accSite = Role::create(['name' => 'accsite']);
        $accSite->givePermissionTo([
            'view_dashboard',
            'view_reports',
            'create_transactions',
            'edit_transactions'
        ]);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([
            'view_dashboard',
            'view_reports'
        ]);
    }
} 