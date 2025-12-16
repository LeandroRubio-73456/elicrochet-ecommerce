<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // 1. Categorías
    $this->call(CategoriesSeeder::class);
    
    
    // 3. Productos
    $this->call(ProductsSeeder::class);
    
}
}
