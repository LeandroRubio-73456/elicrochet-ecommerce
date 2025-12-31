<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPhoneIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock ENV variables for PayPhone
        config(['services.payphone.token' => 'test-token']);
        // Mock Mail to prevent view rendering issues
        \Illuminate\Support\Facades\Mail::fake();
    }

    /** @test */
    public function it_redirects_to_payphone_on_checkout_store()
    {
        // 1. Arrange: Create user, product, and login
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 10, 'stock' => 5]);

        $this->actingAs($user);

        // Add item to cart (assuming you use a service or session, but for integration test we can rely on route behavior if CartService mocks correctly or works with DB)
        // Since CheckoutController uses CartService, and CartService uses darryldecode/cart which uses Session/DB.
        // Let's populate the cart via the actual route or by constructing the session state if possible.
        // For simplicity, let's assume we can hit the 'add to cart' route first.
        $this->post(route('cart.add', $product->id), [
            'quantity' => 1,
        ]);

        // 2. Mock PayPhone 'Prepare' Response
        Http::fake([
            'https://pay.payphonetodoesposible.com/api/button/Prepare' => Http::response([
                'paymentId' => 12345,
                'payWithCard' => 'https://pay.payphone.com.ec/pay-link',
            ], 200),
        ]);

        // Mock CartService to ensure the controller sees items
        $mockCart = $this->mock(\App\Providers\CartService::class);
        $mockCart->shouldReceive('getCart')->andReturn(collect([
            (object) [
                'id' => $product->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 10,
                'custom_order_id' => null,
            ],
        ]));
        $mockCart->shouldReceive('getTotal')->andReturn(10);

        // 3. Act: Submit Checkout Form
        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'John',
            'customer_lastname' => 'Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '0991234567',
            'shipping_address' => 'Av. Test 123',
            'shipping_city' => 'Quito',
            'shipping_province' => 'Pichincha',
            'shipping_zip' => '170101',
        ]);

        // 4. Assert: Order Created and User Redirected to PayPhone
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'total_amount' => 10,
        ]);

        $response->assertRedirect('https://pay.payphone.com.ec/pay-link');
    }

    /** @test */
    public function it_processes_successful_payphone_callback()
    {
        // 1. Arrange: Create an order in pending_payment status
        $user = User::factory()->create();
        $this->actingAs($user); // Ensure logged in
        $address = \App\Models\Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 50, 'stock' => 10]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => 'Calle Falsa 123',
            'shipping_city' => 'Guayaquil',
            'shipping_province' => 'Guayas',
            'shipping_zip' => '090101',
            'total_amount' => 50,
            'address_id' => $address->id, // Use real ID
        ]);

        // Attach item to order
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 50,
        ]);

        // 2. Mock PayPhone 'Confirm' Response
        Http::fake([
            'https://pay.payphonetodoesposible.com/api/button/Confirm' => Http::response([
                'transactionStatus' => 'Approved',
                'amount' => 5000,
                'currency' => 'USD',
            ], 200),
        ]);

        // 3. Act: Hit the callback URL
        // clientTransactionId format is usually "ORDER_ID-TIMESTAMP" based on CheckoutController logic
        $clientTransactionId = $order->id.'-1234567890';

        $response = $this->get(route('checkout.callback', [
            'id' => 99999, // PayPhone Transaction ID
            'clientTransactionId' => $clientTransactionId,
        ]));

        // 4. Assert: Order is Paid and Stock Reduced
        $order->refresh();
        $product->refresh();

        $this->assertEquals(Order::STATUS_PAID, $order->status); // Status Updated
        $this->assertEquals('99999', $order->payphone_transaction_id);
        $this->assertEquals(9, $product->stock); // Stock Reduced (10 - 1)

        $response->assertStatus(200);
        $response->assertViewIs('front.checkout-success');
    }

    /** @test */
    public function it_handles_failed_payphone_callback()
    {
        // 1. Arrange
        // 1. Arrange
        $user = User::factory()->create();
        $this->actingAs($user); // Ensure user is logged in
        $address = \App\Models\Address::factory()->create(['user_id' => $user->id]);
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => 'Calle Falsa 123',
            'shipping_city' => 'Guayaquil',
            'shipping_province' => 'Guayas',
            'shipping_zip' => '090101',
            'total_amount' => 50,
            'address_id' => $address->id,
        ]);

        // 2. Mock PayPhone 'Confirm' Response as REJECTED
        Http::fake([
            'https://pay.payphonetodoesposible.com/api/button/Confirm' => Http::response([
                'transactionStatus' => 'Rejected', // Or Error, Cancelled
            ], 200),
        ]);

        // 3. Act
        $clientTransactionId = $order->id.'-1234567890';
        $response = $this->get(route('checkout.callback', [
            'id' => 88888,
            'clientTransactionId' => $clientTransactionId,
        ]));

        // 4. Assert
        $order->refresh();
        $this->assertEquals('pending_payment', $order->status); // Should NOT change

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('error');
    }
}
