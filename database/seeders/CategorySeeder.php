<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Traditional Sweets (Mithai)' => [
                'Gulab Jamun',
                'Rasgulla',
                'Cham Cham',
                'Khoya Barfi',
                'Kaju Katli',
                'Motichoor Laddu',
                'Besan Laddu',
                'Pera (Dharwad / Mathura)',
                'Habshi Halwa',
                'Sohan Halwa',
                'Patisa',
                'Kala Jamun',
                'Chum Chum Special',
            ],
            'Bakery & Cakes' => [
                'Fresh Cream Cake',
                'Chocolate Fudge Cake',
                'Dry / Plain Cake',
                'Pastries',
                'Biscuits & Cookies',
                'Rusks',
                'Fresh Bread & Buns',
                'Chicken Patties',
            ],
            'Nimko & Snacks' => [
                'Mix Nimko',
                'Daal Moth',
                'Papdi Mix',
                'Crispy Samosa',
                'Chicken Roll',
                'Kachori',
            ],
            'Halwa & Desi Delights' => [
                'Gajar Ka Halwa',
                'Daal Chana Halwa',
                'Panjeeri Special',
                'Akhrot Halwa',
                'Badam Halwa',
            ],
            'Dairy & Beverages' => [
                'Rabri Kheer',
                'Rabri Doodh',
                'Dahi (Yogurt)',
                'Fresh Cream',
                'Kashmiri Chai',
                'Mineral Water & Drinks',
            ],
            'Raw Materials (Ingredients)' => [
                'Sugar (Cheeni)',
                'Flour (Maida)',
                'Pure Desi Ghee',
                'Khoya (Mawa)',
                'Fresh Milk',
                'Dry Fruits (Pista / Badam / Kaju)',
                'Cardamom (Elaichi)',
                'Packaging Boxes & Bags',
            ],
        ];

        foreach ($data as $categoryName => $subcategories) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($subcategories as $sub) {
                Subcategory::firstOrCreate([
                    'category_id' => $category->id,
                    'name'        => $sub,
                ]);
            }
        }

        $this->command->info('✅ Sweet & Bakery Categories & Subcategories seeded successfully.');
    }
}
