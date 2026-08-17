<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Bin Sultan Special',
            'In-House Bakery',
            'Fresh Sweets',
            'Beverages',
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand]);
        }

        $this->command->info('✅ Brands seeded successfully.');
    }
}
