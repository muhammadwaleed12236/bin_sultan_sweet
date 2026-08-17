<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\User;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('email', 'superadmin@admin.com')->first() ?? User::first();
        $admin = User::where('email', 'admin@admin.com')->first();
        $cashier = User::where('email', 'cashier@binsultan.com')->first();

        // Branch 1 - Main Branch
        Branch::firstOrCreate(
            ['name' => 'Main Branch - Bin Sultan Sweet'],
            [
                'name'    => 'Main Branch - Bin Sultan Sweet',
                'address' => 'Main Commercial Market, Karachi',
                'number'  => '0300-1234567',
                'user_id' => $superAdmin ? $superAdmin->id : 1,
            ]
        );

        // Branch 2 - Gulshan Branch (assigned to admin or cashier if exists)
        if ($admin && $admin->id !== ($superAdmin ? $superAdmin->id : 1)) {
            Branch::firstOrCreate(
                ['name' => 'Outlet 2 - Gulshan Branch'],
                [
                    'name'    => 'Outlet 2 - Gulshan Branch',
                    'address' => 'Gulshan-e-Iqbal, Karachi',
                    'number'  => '0300-7654321',
                    'user_id' => $admin->id,
                ]
            );
        }

        $this->command->info('✅ Default Branches created successfully.');
    }
}
