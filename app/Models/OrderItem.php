<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'custom_order_id', 'quantity', 'price', 'custom_description', 'images', 'custom_specs'];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'custom_specs' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customOrder()
    {
        return $this->belongsTo(Order::class, 'custom_order_id');
    }
}
