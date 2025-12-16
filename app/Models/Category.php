<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 
        'description',
        'status', 
        'slug', 
    ];

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => ['success', 'Activo', 'ti-eye'],
            'inactive' => ['warning', 'Inactivo', 'ti-ban'],
            'archived' => ['dark', 'Archivado', 'ti-archive']
        ];
        
        [$color, $text, $icon] = $badges[$this->status] ?? ['secondary', 'Desconocido', 'fa-question'];
        
        return sprintf(
            '<span class="f-12 badge bg-light-%s"><i class="ti %s me-1"></i> %s</span>',
            $color,
            $icon,
            $text
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
