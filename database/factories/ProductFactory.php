<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(2); // مثلاً "Soft Tulip"

        $price = $this->faker->randomFloat(2, 5, 80);

        return [
            'name'            => $name,
            'slug'            => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'category_id'     => null, // رح نعبّيها بالـ seeder
            'price'           => $price,
            'compare_price'   => $this->faker->boolean(40)
                                    ? $price + $this->faker->randomFloat(2, 3, 20)
                                    : null,
            'stock'           => $this->faker->numberBetween(0, 20),
            'is_active'       => $this->faker->boolean(90),
            'weight'          => $this->faker->optional()->randomFloat(2, 0.2, 5),
            'short_description' => $this->faker->optional()->sentence(8),
            'description'     => $this->faker->paragraph(3),
        ];
    }
}
