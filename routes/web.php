<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController as BackProductController;
use App\Http\Controllers\CategoryController as BackCategoryController;
use App\Http\Controllers\Back\UserController as BackUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/404', [HomeController::class, 'notfound'])->name('404');
Route::get('/bestseller', [HomeController::class, 'bestseller'])->name('bestseller');

Route::get('/producto/{slug}', [HomeController::class, 'single'])->name('product.show');
Route::get('/categoria/{slug}', [HomeController::class, 'categoryShow'])->name('category.show');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Rutas del carrito (PROTEGIDAS por auth) - Se mantienen igual por ahora
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product:slug}', [\App\Http\Controllers\CartController::class, 'addToCart'])->name('cart.add'); 
    Route::any('/cart/remove/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout'); // Método index original (home->checkout)
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
    Route::get('/checkout/cancel', function() {
        return redirect()->route('cart')->with('info', 'Pago cancelado por el usuario.');
    })->name('checkout.cancel');
    
    // Pay Existing Order Route
    Route::post('/checkout/pay/{order}', [CheckoutController::class, 'payExisting'])->name('checkout.pay_existing');
});

Route::get('/cart/login-required', [\App\Http\Controllers\CartController::class, 'showMessage'])->name('cart.login-required');

// --- GRUPO ADMIN (Dueña) ---
Route::middleware(['auth', 'verified']) // Idealmente middleware('role:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Productos, Categorías, Usuarios, Órdenes
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
        
        // Eliminar imagen producto
        // Eliminar imagen producto
        Route::delete('/products/images/{productImage}', [\App\Http\Controllers\ProductImageController::class, 'destroy'])->name('products.images.destroy');
        
        // Reporte Financiero
        Route::get('/finance', [\App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('finance.index');
        Route::get('/finance/export', [\App\Http\Controllers\Admin\FinancialController::class, 'export'])->name('finance.export');

    });

// --- GRUPO CUSTOMER (Cliente) ---
Route::middleware(['auth', 'verified'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');
        
        // Perfil y Dirección
        Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');

        // Pedidos
        Route::get('/orders', [\App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Customer\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/confirm', [\App\Http\Controllers\Customer\OrderController::class, 'confirmReceipt'])->name('orders.confirm');

        // Pedido Personalizado
        Route::get('/custom-order', [\App\Http\Controllers\Customer\OrderController::class, 'createCustom'])->name('custom.create');
        Route::post('/custom-order', [\App\Http\Controllers\Customer\OrderController::class, 'storeCustom'])->name('custom.store');
        Route::post('/orders/{order}/add-to-cart', [\App\Http\Controllers\Customer\OrderController::class, 'addCustomToCart'])->name('orders.add_to_cart');
    });

require __DIR__ . '/auth.php';
