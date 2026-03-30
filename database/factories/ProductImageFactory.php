<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        // صور ديكور لطيفة من picsum
        $url = 'https://picsum.photos/seed/' . $this->faker->unique()->word . '/600/600';

        return [
            'product_id' => null, // رح نعبّيها بالـ seeder
            'url'        => $url,
            'file_path'  => null,
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }
}
