<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'order_id',
        'previous_status',
        'new_status',
        'changed_by_user_id',
        'note',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
