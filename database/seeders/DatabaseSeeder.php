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
            BranchSeeder::class,
            WarehouseSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            AccountHeadSeeder::class,
            ProductSeeder::class,
        ]);
    }
}