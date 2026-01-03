<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'price',
        'stock',
        'status',
        'is_featured',
        'specs', // JSON of concrete values
        'average_rating',
        'total_reviews',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'specs' => 'array',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
    ];

    protected function shortDescription(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
            // Limita la descripción a 25 palabras
            Str::words(strip_tags($attributes['description']), 10, '...'),
        );
    }

    // Accesores para estados
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => ['warning', 'Borrador', 'ti-edit'],
            'active' => ['success', 'Activo', 'ti-eye'],
            'out_of_stock' => ['danger', 'Agotado', 'ti-box'],
            'discontinued' => ['secondary', 'Descontinuado', 'ti-na'],
            'archived' => ['dark', 'Archivado', 'ti-archive'],
        ];

        [$color, $text, $icon] = $badges[$this->status] ?? ['secondary', 'Desconocido', 'fa-question'];

        return sprintf(
            '<span class="f-12 badge bg-light-%s"><i class="%s me-1"></i> %s</span>',
            $color,
            $icon,
            $text
        );
    }

    // Scopes útiles
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->where('stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->where('stock', '<=', 0);
            });
    }

    // Métodos de ayuda
    public function isAvailable()
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    public function canBePurchased()
    {
        return $this->isAvailable() && $this->status !== 'discontinued';
    }

    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
