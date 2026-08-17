<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\User;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $category = Category::where('name', 'like', '%Sweet%')->first() ?? Category::first();
        $subCategory = Subcategory::where('category_id', $category->id)->first() ?? Subcategory::first();
        $unitKg = Unit::where('name', 'like', '%Kg%')->first() ?? Unit::first();
        $brand = Brand::first();

        $sampleProducts = [
            [
                'item_name'       => 'Gulab Jamun (Special)',
                'price'           => '1200',
                'wholesale_price' => '1000',
                'alert_quantity'  => 10,
                'initial_stock'   => 50,
            ],
            [
                'item_name'       => 'Rasgulla (Fresh)',
                'price'           => '1200',
                'wholesale_price' => '1000',
                'alert_quantity'  => 10,
                'initial_stock'   => 40,
            ],
            [
                'item_name'       => 'Khoya Barfi',
                'price'           => '1400',
                'wholesale_price' => '1200',
                'alert_quantity'  => 5,
                'initial_stock'   => 30,
            ],
            [
                'item_name'       => 'Kaju Katli',
                'price'           => '2400',
                'wholesale_price' => '2100',
                'alert_quantity'  => 5,
                'initial_stock'   => 20,
            ],
            [
                'item_name'       => 'Motichoor Laddu',
                'price'           => '1000',
                'wholesale_price' => '850',
                'alert_quantity'  => 10,
                'initial_stock'   => 35,
            ],
        ];

        foreach ($sampleProducts as $index => $prod) {
            $itemCode = 'SWT-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            Product::firstOrCreate(
                ['item_name' => $prod['item_name']],
                [
                    'creater_id'      => $adminId,
                    'category_id'     => $category->id,
                    'sub_category_id' => $subCategory ? $subCategory->id : null,
                    'brand_id'        => $brand ? $brand->id : null,
                    'unit_id'         => $unitKg ? $unitKg->id : 1,
                    'item_code'       => $itemCode,
                    'price'           => $prod['price'],
                    'wholesale_price' => $prod['wholesale_price'],
                    'alert_quantity'  => $prod['alert_quantity'],
                    'initial_stock'   => $prod['initial_stock'],
                ]
            );
        }

        $this->command->info('✅ Sample Sweet Products seeded successfully.');
    }
}
