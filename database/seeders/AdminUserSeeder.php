<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create / Get Admin Role
        $role = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        // All Permissions
        $permissions = [
            'Products',
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
        ];

        // Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Give all permissions to Admin
        $role->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // Create Admin User
        $user = User::firstOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        // Assign Admin Role
        $user->syncRoles([$role]);

        $this->command->info('Admin user, role and permissions created successfully.');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: Admin@12345');
    }
}