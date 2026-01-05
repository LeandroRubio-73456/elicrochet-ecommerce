<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Providers\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Assuming database migration is handled or mocked if RefreshDatabase is disabled
    }

    /** @test */
    public function checkout_index_redirects_if_cart_empty()
    {
        $user = User::factory()->make(['id' => 1]); // Mock User

        $this->mock(CartService::class, function ($mock) {
            $mock->shouldReceive('getCart')->andReturn(collect([]));
            $mock->shouldReceive('getTotal')->andReturn(0);
        });

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertRedirect(route('cart'));
    }

    /** @test */
    public function checkout_index_shows_view_if_cart_has_items()
    {
        $user = User::factory()->make(['id' => 1]);

        $this->mock(CartService::class, function ($mock) {
            $product = new Product(['name' => 'Test Product']);
            $product->setRelation('images', collect([])); // Mock images relation

            $mock->shouldReceive('getCart')->andReturn(collect([
                (object) [
                    'product_id' => 1,
                    'quantity' => 1,
                    'price' => 10,
                    'custom_order_id' => null,
                    'product' => $product,
                    'attributes' => [],
                ],
            ]));
            $mock->shouldReceive('getTotal')->andReturn(10);
        });

        $response = $this->actingAs($user)->get(route('checkout'));

        $response->assertStatus(200);
        $response->assertViewIs('front.checkout');
    }

    /** @test */
    public function store_creates_order_and_redirects_to_payphone()
    {
        Http::fake([
            'pay.payphonetodoesposible.com/*' => Http::response(['payWithCard' => 'http://payphone.link'], 200),
        ]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 100]);

        // Use real CartService if possible or mock but hit store()
        $this->mock(CartService::class, function ($mock) use ($product) {
            $mock->shouldReceive('getCart')->andReturn(collect([
                (object) [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 100,
                    'custom_order_id' => null,
                ],
            ]));
            $mock->shouldReceive('getTotal')->andReturn(100);
        });

        $data = [
            'customer_name' => 'John',
            'customer_lastname' => 'Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '123 Street',
            'shipping_city' => 'City',
            'shipping_province' => 'Prov',
            'customer_cedula' => '1712345678', // Added
            'shipping_zip' => '12345',
        ];

        $response = $this->actingAs($user)->post(route('checkout.store'), $data);
        $response->assertRedirect('http://payphone.link');
    }

    /** @test */
    public function payphone_callback_handles_success()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment']);

        Http::fake([
            'pay.payphonetodoesposible.com/api/button/Confirm' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $response = $this->actingAs($user)->get(route('checkout.callback', [
            'id' => 'trans-123',
            'clientTransactionId' => $order->id.'-time',
        ]));

        $response->assertStatus(200); // success view
        $this->assertEquals('paid', $order->fresh()->status);
    }

    /** @test */
    public function payphone_callback_handles_failure()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment']);

        Http::fake([
            'pay.payphonetodoesposible.com/api/button/Confirm' => Http::response(['transactionStatus' => 'Declined'], 200),
        ]);

        $response = $this->actingAs($user)->get(route('checkout.callback', [
            'id' => 'trans-123',
            'clientTransactionId' => $order->id.'-time',
        ]));

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function decrement_stock_fails_if_insufficient()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 0]); // Out of stock now
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment']);
        $order->items()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => 10]);

        Http::fake([
            'pay.payphonetodoesposible.com/api/button/Confirm' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $response = $this->actingAs($user)->get(route('checkout.callback', [
            'id' => 'trans-123',
            'clientTransactionId' => $order->id.'-time',
        ]));

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('error', 'Error procesando el pedido: Stock insuficiente para el producto \''.$product->name.'\'. La compra ha sido revertida.');
    }

    /** @test */
    public function it_can_pay_existing_order()
    {
        Http::fake([
            'pay.payphonetodoesposible.com/*' => Http::response(['payWithCard' => 'http://payphone.link/existing'], 200),
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment', 'total_amount' => 50]);

        $response = $this->actingAs($user)->post(route('checkout.pay_existing', $order));

        $response->assertRedirect('http://payphone.link/existing');
    }

    /** @test */
    public function it_reuses_existing_pending_order_during_store()
    {
        Http::fake([
            'pay.payphonetodoesposible.com/*' => Http::response(['payWithCard' => 'http://payphone.link'], 200),
        ]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 100]);
        $existingOrder = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'type' => 'stock',
        ]);

        $this->mock(CartService::class, function ($mock) use ($product) {
            $mock->shouldReceive('getCart')->andReturn(collect([
                (object) ['product_id' => $product->id, 'quantity' => 1, 'price' => 100, 'custom_order_id' => null],
            ]));
            $mock->shouldReceive('getTotal')->andReturn(100);
        });

        $data = [
            'customer_name' => 'John',
            'customer_lastname' => 'Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '123 Street',
            'shipping_city' => 'Quito',
            'shipping_province' => 'Pichincha',
            'customer_cedula' => '1712345678',
            'shipping_zip' => '12345',
        ];

        $this->actingAs($user)->post(route('checkout.store'), $data);

        $this->assertEquals(1, Order::where('user_id', $user->id)->count());
        $this->assertEquals($existingOrder->id, Order::where('user_id', $user->id)->first()->id);
    }

    /** @test */
    public function it_marks_product_as_draft_when_stock_reaches_zero_after_payment()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1, 'status' => 'active']);
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment']);
        $order->items()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => 10]);

        Http::fake([
            'pay.payphonetodoesposible.com/api/button/Confirm' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $this->actingAs($user)->get(route('checkout.callback', [
            'id' => 'trans-123',
            'clientTransactionId' => $order->id.'-time',
        ]));

        $this->assertEquals(0, $product->fresh()->stock);
        $this->assertEquals('draft', $product->fresh()->status);
    }

    /** @test */
    public function it_links_custom_orders_on_payment_success()
    {
        $user = User::factory()->create();
        $customOrder = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment', 'type' => 'custom']);
        $masterOrder = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending_payment']);
        $masterOrder->items()->create([
            'custom_order_id' => $customOrder->id,
            'price' => 50,
            'quantity' => 1,
        ]);

        Http::fake([
            'pay.payphonetodoesposible.com/api/button/Confirm' => Http::response(['transactionStatus' => 'Approved'], 200),
        ]);

        $this->actingAs($user)->get(route('checkout.callback', [
            'id' => 'trans-123',
            'clientTransactionId' => $masterOrder->id.'-time',
        ]));

        $this->assertEquals('linked', $customOrder->fresh()->status);
        $this->assertEquals('paid', $masterOrder->fresh()->status);
    }
}
