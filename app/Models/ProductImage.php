<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'file_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // نخلي الـ url يرجع تلقائياً مع الموديل
    protected $appends = [
        'url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* Accessor يرجع رابط الصورة الجاهز للعرض */
    public function getUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        // لو مخزّن لينك كامل (CDN مثلاً)
        if (Str::startsWith($this->file_path, ['http://', 'https://'])) {
            return $this->file_path;
        }

        // مسار داخل storage/app/public
        return asset('storage/' . ltrim($this->file_path, '/'));
    }
}
