<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Providers\CartService;
use Illuminate\Support\Facades\Auth;

// Setup User
$user = User::first(); 
Auth::login($user);
CartItem::where('user_id', $user->id)->delete();

echo "User: {$user->id}\n";

// 1. Create Custom Order A (The "Master")
$orderA = Order::create([
    'user_id' => $user->id,
    'status' => 'pending_payment',
    'total_amount' => 50.00,
    'type' => 'custom',
    'customer_name' => 'User A',
    'customer_email' => 'a@test.com',
    'customer_phone' => '123'
]);
// Item for A
OrderItem::create([
    'order_id' => $orderA->id,
    'product_id' => null,
    'custom_description' => 'Custom A Description',
    'price' => 50.00,
    'quantity' => 1
]);
echo "Created Order A: {$orderA->id}\n";

// 2. Create Custom Order B (The "Secondary")
$orderB = Order::create([
    'user_id' => $user->id,
    'status' => 'pending_payment',
    'total_amount' => 75.00,
    'type' => 'custom',
    'customer_name' => 'User A',
    'customer_email' => 'a@test.com',
    'customer_phone' => '123'
]);
// Item for B
OrderItem::create([
    'order_id' => $orderB->id,
    'product_id' => null,
    'custom_description' => 'Custom B Description',
    'price' => 75.00,
    'quantity' => 1
]);
echo "Created Order B: {$orderB->id}\n";

// 3. Add both to Cart
$cartService = new CartService();
$cartService->addCustomOrder($orderA);
$cartService->addCustomOrder($orderB);

echo "Cart Count: " . $cartService->getCount() . "\n";

// 4. Simulate Checkout (Dry Run of Logic)
$cartItems = $cartService->getCart();
$customOrdersInCart = $cartItems->whereNotNull('custom_order_id');

echo "Custom Orders found in cart: " . $customOrdersInCart->count() . "\n";

$masterOrder = null;

// LOGIC REPLICATION
foreach($customOrdersInCart as $item) {
    if (!$masterOrder) {
        $masterOrder = Order::find($item->custom_order_id);
        echo "Selected Master Order: {$masterOrder->id}\n";
    } else {
        echo "Found Secondary Order: {$item->custom_order_id}\n";
    }
}

// Current Bug Check: Do we merge B into A correctly?
// The current implementation loop inside CheckoutController looks like:
/*
foreach ($cartItems as $cartItem) {
    if ($cartItem->custom_order_id) {
         $existingCustomItem = $order->items()->whereNull('product_id')->first();
         if ($existingCustomItem) {
             // UPDATE
         }
         continue;
    }
}
*/
// ISSUE: If we have multiple, they ALL hit this `if`.
// 1. CartItem A hits it. Updates Order A's item.
// 2. CartItem B hits it. It checks `$order` (which is Master A).
//    It finds `existingCustomItem` (Order A's item).
//    It UPDATES IT AGAIN with Order B's price.
//    Order B's specific description/item is NEVER copied over.
//    Order B itself remains in DB as 'in_cart' forever or zombie.

echo "--- Logic Analysis Completed ---\n";
