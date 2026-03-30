<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * الأدوار المتوفرة (حسب الداتا عندك)
     */
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_blocked',
        'role',
    ];

    /**
     * الحقول اللي بتنخفي عند الإرجاع JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * التحويلات
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_blocked'        => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | علاقات اليوزر
    |--------------------------------------------------------------------------
    */

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | دوال الأدوار
    |--------------------------------------------------------------------------
    */

    /** هل المستخدم أدمن؟ */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** هل المستخدم زبون عادي؟ */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }
}
