<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [HomeController::class, 'shop'])->name('shop');

Route::get('/404', [HomeController::class, 'notfound'])->name('404');

Route::get('/bestseller', [HomeController::class, 'bestseller'])->name('bestseller');

Route::get('/cart', [HomeController::class, 'cart'])->name('cart');

Route::get('/cheackout', [HomeController::class, 'cheackout'])->name('cheackout');

Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::get('/single', [HomeController::class, 'single'])->name('single');

Route::get('/producto/{slug}', [HomeController::class, 'single'])
    ->name('product.show');

Route::get('/categoria/{slug}', [HomeController::class, 'categoryShow'])->name('category.show');

Route::delete(
    '/products/images/{productImage}', // Cambiamos {image} por {productImage}
    [\App\Http\Controllers\ProductImageController::class, 'destroy']
)->name('back.products.images.destroy');

Route::get('/dashboard', function () {
    return view('back.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. GRUPO DE RUTAS DE RECURSO CON PREFIJOS
Route::middleware(['auth'])
    ->prefix('dashboard')
    ->name('back.')
    ->group(function () {

        // Ahora esta ruta genera: admin.products.index, admin.products.edit, etc.
        Route::resource('products', ProductController::class);

        Route::resource('categories', CategoryController::class);
    });

require __DIR__ . '/auth.php';
