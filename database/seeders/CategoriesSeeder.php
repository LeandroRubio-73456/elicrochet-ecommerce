<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Amigurumis', 'slug' => 'amigurumis'],
            ['name' => 'Accesorios', 'slug' => 'accesorios'],
            ['name' => 'Ropa', 'slug' => 'ropa'],
            ['name' => 'Decoración', 'slug' => 'decoracion'],
            ['name' => 'Kit Iniciación', 'slug' => 'kit-iniciacion'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
