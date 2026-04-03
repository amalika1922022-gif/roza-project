<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    //
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'compare_price',
        'stock',
        'is_active',
        'weight',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderByDesc('is_primary')  // خلي الصورة الأساسية أول وحدة
            ->orderBy('sort_order')      // بعدين حسب الترتيب اليدوي (لو مستخدمته)
            ->orderBy('id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function homepageCarouselItem()
    {
        return $this->hasOne(HomepageCarouselItem::class);
    }
}
