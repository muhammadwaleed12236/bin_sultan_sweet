<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Kg (Kilogram)',
            'Gram (g)',
            'Box (Dabba)',
            'Piece (Pcs)',
            'Dozen (Darjan)',
            'Pack',
            'Litre (L)',
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit]);
        }

        $this->command->info('✅ Units seeded successfully.');
    }
}
