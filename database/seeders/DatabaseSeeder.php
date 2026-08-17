<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            AdminUserSeeder::class,
            BranchSeeder::class,
            WarehouseSeeder::class,
            UnitSeeder::class,
            AccountHeadSeeder::class,
        ]);
    }
}