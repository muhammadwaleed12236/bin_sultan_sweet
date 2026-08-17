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

        if (Branch::count() === 0) {
            Branch::create([
                'name'    => 'Main Branch - Bin Sultan Sweet',
                'address' => 'Latifabad no 6 Near Shadman Hall Hyderabad',
                'number'  => '022 2786661',
                'user_id' => $superAdmin ? $superAdmin->id : 1,
            ]);
        }

        $this->command->info('✅ Default Branches created successfully.');
    }
}
