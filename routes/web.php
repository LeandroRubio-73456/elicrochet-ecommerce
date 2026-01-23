<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\HomeController;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/404', [HomeController::class, 'notfound'])->name('404');
Route::get('/bestseller', [HomeController::class, 'bestseller'])->name('bestseller');

Route::get('/producto/{slug}', [HomeController::class, 'single'])->name('product.show');
Route::get('/categoria/{slug}', [HomeController::class, 'categoryShow'])->name('category.show');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'storeContact'])->name('contact.store');

Route::view('/devoluciones', 'front.legal.returns')->name('legal.returns');
Route::view('/terminos-condiciones', 'front.legal.terms')->name('legal.terms');

// Rutas del carrito (PROTEGIDAS por auth) - Se mantienen igual por ahora
Route::middleware(['auth'])->group(function () {
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('/cart', [\App\Http\Controllers\Front\CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product:slug}', [\App\Http\Controllers\Front\CartController::class, 'addToCart'])->name('cart.add');
    Route::any('/cart/remove/{id}', [\App\Http\Controllers\Front\CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update', [\App\Http\Controllers\Front\CartController::class, 'update'])->name('cart.update');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout'); // Método index original (home->checkout)
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
    Route::get('/checkout/cancel', function () {
        // Delete any pending parent orders for this user to avoid confusion
        if (Auth::check()) {
            \App\Models\Order::where('user_id', Auth::id())
                ->whereIn('type', [Order::TYPE_STOCK, 'stock'])
                ->where('status', \App\Models\Order::STATUS_PENDING_PAYMENT)
                ->delete();
        }

        return redirect()->route('cart')->with('info', 'Pago cancelado por el usuario.');
    })->name('checkout.cancel');

    // Pay Existing Order Route
    // Pay Existing Order Route
    Route::post('/checkout/pay/{order}', [CheckoutController::class, 'payExisting'])->name('checkout.pay_existing');

    // Reviews
    Route::post('/products/{product:slug}/reviews', [App\Http\Controllers\Front\ReviewController::class, 'store'])->name('reviews.store');
});

Route::get('/cart/login-required', [\App\Http\Controllers\Front\CartController::class, 'showMessage'])->name('cart.login-required');

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
        Route::get('/orders/{order}/label', [\App\Http\Controllers\Admin\OrderController::class, 'generateLabel'])->name('orders.label');

        // Eliminar imagen producto
        Route::delete('/products/images/{productImage}', [\App\Http\Controllers\Admin\ProductImageController::class, 'destroy'])->name('products.images.destroy');

        // Reporte Financiero
        Route::get('/finance', [\App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('finance.index');
        Route::get('/finance/export', [\App\Http\Controllers\Admin\FinancialController::class, 'export'])->name('finance.export');

    });

// --- GRUPO ACCOUNT (Cliente) ---
Route::middleware(['auth', 'verified'])
    ->prefix('account')
    ->name('account.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('index'); // Dashboard

        // Perfil y Dirección
        Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'destroy'])->name('profile.destroy');

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

// --- ROUTES FOR FRONT CONTROLLERS (For coverage and public use) ---

// Google Auth
Route::get('auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
