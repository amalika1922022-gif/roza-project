<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'order_id',
        'provider',
        'provider_payment_id',
        'amount',
        'currency',
        'status',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
