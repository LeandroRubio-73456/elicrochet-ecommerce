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
        Product::truncate();

        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            // Amigurumis
            [
                'name' => 'Unicornio Mágico',
                'slug' => 'unicornio-magico',
                'description' => 'Un adorable unicornio tejido con hilo de algodón hipoalergénico. Ideal para niños de todas las edades. Mide 30cm de alto.',
                'price' => 35.00,
                'stock' => 10,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'amigurumis'
            ],
            [
                'name' => 'Osito Dormilón',
                'slug' => 'osito-dormilon',
                'description' => 'Osito de peluche tejido a crochet, perfecto para acompañar el sueño del bebé.',
                'price' => 28.50,
                'stock' => 5,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'amigurumis'
            ],
             // Ropa
            [
                'name' => 'Gorro de Lana Merino',
                'slug' => 'gorro-lana-merino',
                'description' => 'Gorro tejido a mano con 100% lana merino, muy suave y abrigado para el invierno.',
                'price' => 25.00,
                'stock' => 20,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'ropa-y-accesorios'
            ],
            [
                'name' => 'Bufanda Infinita',
                'slug' => 'bufanda-infinita',
                'description' => 'Bufanda circular de punto grueso, disponible en varios colores.',
                'price' => 30.00,
                'stock' => 15,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'ropa-y-accesorios'
            ],
            // Hogar
            [
                'name' => 'Cesta Organizadora',
                'slug' => 'cesta-organizadora',
                'description' => 'Cesta tejida en trapillo, ideal para organizar el baño o el dormitorio.',
                'price' => 18.00,
                'stock' => 12,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'hogar-y-decoracion'
            ],
             [
                'name' => 'Posavasos Boho (Set x4)',
                'slug' => 'posavasos-boho',
                'description' => 'Set de 4 posavasos estilo bohemio tejidos en macramé.',
                'price' => 15.00,
                'stock' => 30,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'hogar-y-decoracion'
            ],
            // Bebes
             [
                'name' => 'Escarpines Recién Nacido',
                'slug' => 'escarpines-rn',
                'description' => 'Zapatitos tejidos para bebés de 0 a 3 meses. Lana suave que no pica.',
                'price' => 12.00,
                'stock' => 25,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'bebes'
            ],
             [
                'name' => 'Manta de Apego Conejito',
                'slug' => 'manta-apego-conejito',
                'description' => 'Mini manta con cabeza de conejito, reconfortante para el bebé.',
                'price' => 22.00,
                'stock' => 8,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'bebes'
            ],
             // Patrones (Digital)
             [
                'name' => 'Patrón PDF - Muñeca Lola',
                'slug' => 'patron-pdf-muneca-lola',
                'description' => 'Archivo descargable con las instrucciones para tejer la muñeca Lola. Nivel intermedio.',
                'price' => 5.00,
                'stock' => 999, 
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'patrones-y-guias'
            ],
            [
                'name' => 'Llavero de Corazón',
                'slug' => 'llavero-corazon',
                'description' => 'Pequeño llavero en forma de corazón, ideal para regalar.',
                'price' => 5.00,
                'stock' => 50,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'amigurumis'
            ],
            [
                'name' => 'Muñeca Sofía',
                'slug' => 'muneca-sofia',
                'description' => 'Muñeca personalizada con vestido de colores. 25cm de alto.',
                'price' => 45.00,
                'stock' => 3,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'amigurumis'
            ],
            [
                'name' => 'Chaleco Bohemio',
                'slug' => 'chaleco-bohemio',
                'description' => 'Chaleco de hilo estilo boho chic, talla única adaptable.',
                'price' => 55.00,
                'stock' => 8,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'ropa-y-accesorios'
            ],
            [
                'name' => 'Top Crop Verano',
                'slug' => 'top-crop-verano',
                'description' => 'Top fresco tejido a crochet, ideal para la playa o días de calor.',
                'price' => 22.00,
                'stock' => 15,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'ropa-y-accesorios'
            ],
            [
                'name' => 'Alfombra de Trapillo Redonda',
                'slug' => 'alfombra-trapillo-redonda',
                'description' => 'Alfombra de 1 metro de diámetro tejida a mano con trapillo reciclado.',
                'price' => 60.00,
                'stock' => 4,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'hogar-y-decoracion'
            ],
            [
                'name' => 'Funda de Cojín Texturizada',
                'slug' => 'funda-cojin-texturizada',
                'description' => 'Funda para cojín de 40x40cm con diseño de texturas en relieve.',
                'price' => 28.00,
                'stock' => 10,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'hogar-y-decoracion'
            ],
            [
                'name' => 'Cesta Colgante para Plantas',
                'slug' => 'cesta-colgante-plantas',
                'description' => 'Macetero colgante de macramé y crochet para decorar tus rincones.',
                'price' => 20.00,
                'stock' => 20,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'hogar-y-decoracion'
            ],
            [
                'name' => 'Set de Nacimiento (Gorro + Escarpines)',
                'slug' => 'set-nacimiento',
                'description' => 'Hermoso set para regalo de bienvenida al bebé.',
                'price' => 35.00,
                'stock' => 6,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'bebes'
            ],
            [
                'name' => 'Sonajero Mordedor',
                'slug' => 'sonajero-mordedor',
                'description' => 'Sonajero de madera natural y crochet, seguro para el bebé.',
                'price' => 14.00,
                'stock' => 18,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'bebes'
            ],
            [
                'name' => 'Patrón PDF - Top Halter',
                'slug' => 'patron-pdf-top-halter',
                'description' => 'Guía paso a paso para tejer tu propio top halter. Incluye diagramas.',
                'price' => 6.50,
                'stock' => 999,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'patrones-y-guias'
            ],
            [
                'name' => 'Patrón PDF - Manta XXL',
                'slug' => 'patron-pdf-manta-xxl',
                'description' => 'Instrucciones para tejer una manta gigante con vellón de lana.',
                'price' => 4.00,
                'stock' => 999,
                'status' => 'active',
                'is_featured' => false,
                'category_slug' => 'patrones-y-guias'
            ],
        ];

        foreach ($products as $data) {
            $categorySlug = $data['category_slug'];
            unset($data['category_slug']); // Remove helper key

            $category = $categories->firstWhere('slug', $categorySlug);

            if ($category) {
                $data['category_id'] = $category->id;
                Product::create($data);
            }
        }
    }
}
