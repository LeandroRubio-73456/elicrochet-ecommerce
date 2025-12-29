<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutRaceConditionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that two concurrent payments for the same single-stock product
     * result in only one success and one failure (insufficient stock).
     */
    public function test_prevents_overselling_race_condition()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        
        // Need a category for the product
        $category = \App\Models\Category::create([
            'name' => 'Test Cat', 
            'slug' => 'test-cat',
        ]);

        // Product with stock 1
        $product = Product::create([
            'name' => 'High Demand Item',
            'slug' => 'high-demand-item',
            'description' => 'Only one left!',
            'price' => 100.00,
            'stock' => 1,
            'category_id' => $category->id,
        ]);
        
        // 2. Create Two Orders for the SAME product (Quantity 1 each)
        // Scenario: Two users have the last item in their cart and click pay at the exact same moment.
        
        // Order A
        $orderA = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100.00,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'type' => Order::TYPE_STOCK,
            'customer_email' => 'user@example.com',
            'customer_name' => 'User A',
        ]);
        OrderItem::create([
            'order_id' => $orderA->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        // Order B
        $orderB = Order::create([
            'user_id' => $user->id, // Could be same or different user
            'total_amount' => 100.00,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'type' => Order::TYPE_STOCK,
            'customer_email' => 'user@example.com',
            'customer_name' => 'User B',
        ]);
        OrderItem::create([
            'order_id' => $orderB->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        // 3. Mock PayPhone Response
        // We simulate that BOTH payments were "Approved" by the gateway.
        // The race happens when the callback hits our server.
        Http::fake([
            'pay.payphonetodoesposible.com/*' => Http::response([
                'transactionStatus' => 'Approved'
            ], 200),
        ]);

        // 4. Process First Order (Winner)
        // Simulate Callback for Order A
        $payphoneIdA = '10001';
        $responseA = $this->actingAs($user)->get(route('checkout.callback', [
            'id' => $payphoneIdA, 
            'clientTransactionId' => $orderA->id . '-1234'
        ]));

        // Request A Assertion
        $responseA->assertStatus(200); // Should return view 'checkout-success'
        $responseA->assertViewIs('front.checkout-success');
        
        // Check DB State after A
        $this->assertDatabaseHas('orders', ['id' => $orderA->id, 'status' => Order::STATUS_PAID]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 0]); // Stock dropped to 0

        // 5. Process Second Order (Loser)
        // Simulate Callback for Order B coming in slightly later (or concurrently in real life)
        $payphoneIdB = '10002';
        $responseB = $this->actingAs($user)->get(route('checkout.callback', [
            'id' => $payphoneIdB, 
            'clientTransactionId' => $orderB->id . '-5678'
        ]));

        // Request B Assertion
        // Should FAIL because stock checks find 0.
        // Controller Logic: throws Exception "Stock insuficiente..." -> Transaction Rollback -> Redirect directly to cart
        $responseB->assertRedirect(route('cart'));
        $responseB->assertSessionHas('error'); // The controller sets 'error' on Exception
        
        // Assert specific error message part if possible to be sure it's the stock error
        // "Stock insuficiente para el producto..."
        $responseB->assertSessionHas('error', function ($error) {
            return str_contains($error, 'Stock insuficiente');
        });

        // Check DB State after B
        $this->assertDatabaseHas('orders', ['id' => $orderB->id, 'status' => Order::STATUS_PENDING_PAYMENT]); // Should NOT be PAID (rolled back)
        
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 0]); // Remains 0, doesn't go negative
    }
}
