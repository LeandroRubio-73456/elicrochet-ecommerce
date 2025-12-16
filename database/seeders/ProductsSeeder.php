<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $categories = Category::all();
    
    $products = [
        [
            'name' => 'Conjunto Gorro y Bufanda Navideña',
            'slug' => 'gorro-bufanda-navidad',
            'description' => 'Conjunto tejido en lana merino, ideal para invierno',
            'price' => 45.99,
            'stock' => 5,
            'category_id' => $categories->where('slug', 'ropa')->first()->id,
        ],
        [
            'name' => 'Amigurumi Unicornio Mágico',
            'slug' => 'unicornio-amigurumi',
            'description' => 'Unicornio de 25cm con crin arcoíris',
            'price' => 32.50,
            'stock' => 8,
            'category_id' => $categories->where('slug', 'amigurumis')->first()->id,
        ],
        // ... más productos
    ];
    
    foreach ($products as $product) {
        Product::create($product);
    }
}
}
