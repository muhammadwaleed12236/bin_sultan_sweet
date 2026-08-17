<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\User;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::first();
        $branchId = $branch ? $branch->id : 1;

        $adminUser = User::first();
        $adminId = $adminUser ? $adminUser->id : 1;

        $warehouses = [
            [
                'branch_id'      => $branchId,
                'warehouse_name' => 'Main Warehouse (Godown)',
                'creater_id'     => $adminId,
                'location'       => 'Karachi Central',
                'remarks'        => 'Central bulk storage and inventory godown',
            ],
            [
                'branch_id'      => $branchId,
                'warehouse_name' => 'Kitchen / Production Unit',
                'creater_id'     => $adminId,
                'location'       => 'Production Plant',
                'remarks'        => 'Raw materials and fresh sweet production store',
            ],
            [
                'branch_id'      => $branchId,
                'warehouse_name' => 'Front Counter / Retail Display',
                'creater_id'     => $adminId,
                'location'       => 'Main Shop Counter',
                'remarks'        => 'Active retail POS and takeaway stock',
            ],
        ];

        foreach ($warehouses as $data) {
            Warehouse::firstOrCreate(
                ['warehouse_name' => $data['warehouse_name']],
                $data
            );
        }

        $this->command->info('✅ Default Warehouses created successfully.');
    }
}
