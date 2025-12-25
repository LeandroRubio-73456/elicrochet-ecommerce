<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'custom_order_id', // Added
        'quantity',
        'price',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customOrder()
    {
        return $this->belongsTo(Order::class, 'custom_order_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
