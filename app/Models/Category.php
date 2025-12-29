<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'icon',
        'required_specs', // array of field definitions
    ];

    protected $casts = [
        'required_specs' => 'array',
    ];

    /**
     * Validate input against required specs
     */
    public function validateSpecs(array $input)
    {
        $errors = [];
        $specs = $this->required_specs ?? [];

        foreach ($specs as $spec) {
            $fieldName = $spec['name'];
            $fieldType = $spec['type'] ?? 'text';
            $fieldRequired = $spec['required'] ?? false;

            if ($fieldRequired && empty($input[$fieldName])) {
                $errors[] = "El campo '{$fieldName}' es obligatorio.";

                continue;
            }

            // Type validation (basic)
            if (! empty($input[$fieldName])) {
                if ($fieldType === 'number' && ! is_numeric($input[$fieldName])) {
                    $errors[] = "El campo '{$fieldName}' debe ser numérico.";
                }
                // Add more type checks as needed
            }
        }

        return $errors;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => ['success', 'Activo', 'ti-eye'],
            'inactive' => ['warning', 'Inactivo', 'ti-na'],
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
