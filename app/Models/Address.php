<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'phone',
        'street',
        'address',
        'reference',
        'city',
        'province',
        'postal_code',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
