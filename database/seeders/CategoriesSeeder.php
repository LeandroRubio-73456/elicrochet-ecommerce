<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        Category::truncate();

        $categories = [
            [
                'name' => 'Amigurumis',
                'slug' => 'amigurumis',
                'description' => 'Muñecos tejidos a crochet ideales para regalos.',
                'icon' => 'ti-mood-smile',
                'status' => 'active',
                'required_specs' => [
                    ['name' => 'Altura (cm)', 'type' => 'number', 'required' => true],
                    ['name' => 'Material', 'type' => 'select', 'options' => ['Algodón', 'Lana', 'Hipoalergénico'], 'required' => false],
                ],
            ],
            [
                'name' => 'Ropa y Accesorios',
                'slug' => 'ropa-y-accesorios',
                'description' => 'Prendas y complementos tejidos a mano con materiales de alta calidad.',
                'icon' => 'ti-shirt',
                'status' => 'active',
                'required_specs' => [
                    ['name' => 'Talla', 'type' => 'select', 'options' => ['XS', 'S', 'M', 'L', 'XL'], 'required' => true],
                    ['name' => 'Color Principal', 'type' => 'text', 'required' => true],
                ],
            ],
            [
                'name' => 'Hogar y Decoración',
                'slug' => 'hogar-y-decoracion',
                'description' => 'Dale un toque cálido a tu hogar con estos artículos tejidos.',
                'icon' => 'ti-home',
                'status' => 'active',
                'required_specs' => [],
            ],
            [
                'name' => 'Bebés',
                'slug' => 'bebes',
                'description' => 'Ropita y juguetes seguros y suaves para los más pequeños.',
                'icon' => 'ti-baby-carriage',
                'status' => 'active',
                'required_specs' => [['name' => 'Edad (Meses)', 'type' => 'number']],
            ],
            [
                'name' => 'Patrones y Guías',
                'slug' => 'patrones-y-guias',
                'description' => 'Instrucciones paso a paso para crear tus propios tejidos.',
                'icon' => 'ti-file-text',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
