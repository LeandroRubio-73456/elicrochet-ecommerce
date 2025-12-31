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
}
