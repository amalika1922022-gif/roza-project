<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Flowers',
            'Vases & Pots',
            'Candles',
            'Wall Decor',
            'Table Decor',
            'Gift Boxes',
        ];

        foreach ($names as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // لو حابب كمان كم كاتيجوري عشوائي
        Category::factory()->count(4)->create();
    }
}
