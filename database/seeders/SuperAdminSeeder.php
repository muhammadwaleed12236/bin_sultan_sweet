<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. All system permissions
        $permissions = [
            'Products',
            'Create Product',
            'Delete Product',
            'View Product',
            'Edit Product',
            'Discount Products',
            'Category',
            'Sub Category',
            'Brands',
            'List Inwards',
            'Create Inward Gatepass',
            'Purchase',
            'Purchase Return',
            'Vendor',
            'List Warehouse',
            'Warehouse Stock',
            'Stock Transfer',
            'Sales',
            'Sale Return',
            'Bookings',
            'Customer',
            'Sales Officer',
            'Zone',
            'Char Of Accounts',
            'Narrations',
            'Receipts Voucher',
            'Payment Voucher',
            'Expense Voucher',
            'Item Stock Report',
            'Purchase Report',
            'Sale Report',
            'Customer Ledger',
            'Vendor Ledger',
            'System Reports',
            'specific product',
            'Raw Materials',
            'Raw Material Stock Report',
            'Raw Material Purchase',
            'Raw Material Out',
        ];

        // Create Permissions if not exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $allPermissions = Permission::where('guard_name', 'web')->get();

        // 2. Roles
        $rolesToSyncAll = ['super-admin', 'Super Admin', 'Admin', 'admin'];
        $roleObjects = [];
        foreach ($rolesToSyncAll as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($allPermissions);
            $roleObjects[$roleName] = $role;
        }

        // Branch Role
        $branchRole = Role::firstOrCreate([
            'name' => 'branch',
            'guard_name' => 'web',
        ]);
        $branchPermissions = Permission::whereIn('name', [
            'Sales',
            'Sale Return',
            'Bookings',
            'Customer',
            'Products',
            'View Product',
            'List Inwards',
            'Create Inward Gatepass',
            'Warehouse Stock',
            'Stock Transfer',
        ])->get();
        $branchRole->syncPermissions($branchPermissions);

        // 3. Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('12345678'),
                'usertype' => 'admin',
            ]
        );
        $superAdmin->syncRoles([$roleObjects['super-admin'], $roleObjects['Admin']]);
        $superAdmin->syncPermissions($allPermissions);

        // 4. Admin User (admin@admin.com)
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('12345678'),
                'usertype' => 'admin',
            ]
        );
        $admin->syncRoles([$roleObjects['Admin'], $roleObjects['super-admin']]);
        $admin->syncPermissions($allPermissions);

        // 5. Default Branch / Cashier User
        $cashier = User::updateOrCreate(
            ['email' => 'cashier@binsultan.com'],
            [
                'name'     => 'Branch Cashier',
                'password' => Hash::make('12345678'),
                'usertype' => 'user',
            ]
        );
        $cashier->syncRoles([$branchRole]);

        $this->command->info('----------------------------------------------------');
        $this->command->info('✅ Super Admin & Roles Setup Completed Successfully:');
        $this->command->info('1) Super Admin: superadmin@admin.com | Password: 12345678');
        $this->command->info('2) Admin User : admin@admin.com      | Password: 12345678');
        $this->command->info('3) Cashier    : cashier@binsultan.com | Password: 12345678');
        $this->command->info('----------------------------------------------------');
    }
}
