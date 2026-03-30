<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReservation extends Model
{
    protected $fillable = [
        'order_id','product_id','qty','status','expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
