<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // لمسح البيانات القديمة (للتطوير فقط)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProductImage::truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // جلب كل الـ categories
        $categories = Category::pluck('id')->all();

        if (empty($categories)) {
            return;
        }

        // أسماء منتجات تجريبية
        $productsData = [
            'Rose Bouquet',
            'Tulip Mix',
            'Lavender Candle',
            'Vanilla Scent',
            'Gift Box – Small',
            'Gift Box – Large',
            'Wall Art – Abstract',
            'Decorative Vase',
            'Ceramic Mug',
            'Pillow – Soft Pink',
            'Pillow – Grey',
            'Scented Sticks',
        ];

        // روابط صور عشوائية
        $sampleImages = [
            'https://images.pexels.com/photos/931162/pexels-photo-931162.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/102129/pexels-photo-102129.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/102104/pexels-photo-102104.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/931177/pexels-photo-931177.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1477166/pexels-photo-1477166.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/94842/pexels-photo-94842.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1866149/pexels-photo-1866149.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1022923/pexels-photo-1022923.jpeg?auto=compress&cs=tinysrgb&w=800',
        ];

        foreach ($productsData as $name) {
            // إنشاء المنتج
            $product = Product::create([
                'name'          => $name,
                'slug'          => Str::slug($name) . '-' . rand(100, 999),
                'category_id'   => collect($categories)->random(),
                'price'         => rand(5, 120),
                'compare_price' => null,              // عدليها حسب جدولك
                'stock'         => rand(3, 20),
                'is_active'     => true,
                'description'   => 'This is a demo product seeded for testing the storefront, cart, and checkout pages.',
                'weight'        => null,              // عدليها لو ما عندك العمود
            ]);

            // 1–3 صور لكل منتج
            $imagesCount = rand(1, 3);

            for ($i = 0; $i < $imagesCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    // نخزن الرابط داخل file_path، والـ accessor getUrlAttribute
                    // رح يرجعو مباشرة لأنه يبدأ بـ https
                    'file_path'  => $sampleImages[array_rand($sampleImages)],
                    'is_primary' => $i === 0,
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}
