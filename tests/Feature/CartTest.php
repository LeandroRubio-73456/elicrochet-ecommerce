<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Providers\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cart_index_displays_items_and_total()
    {
        $user = User::factory()->create();

        $this->mock(CartService::class, function ($mock) {
            $mock->shouldReceive('getCart')->once()->andReturn(collect([]));
            $mock->shouldReceive('getTotal')->once()->andReturn(0);
        });

        $response = $this->actingAs($user)->get(route('cart'));

        $response->assertStatus(200);
        $response->assertViewIs('front.cart');
        $response->assertViewHas(['cartItems', 'cartTotal']);
    }

    /** @test */
    public function user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 100]);

        $this->mock(CartService::class, function ($mock) use ($product) {
            $mock->shouldReceive('addToCart')
                ->once()
                ->with(\Mockery::on(function ($arg) use ($product) {
                    return $arg->id === $product->id;
                }), 1, \Mockery::any());
        });

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 1]);

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function cannot_add_more_than_stock()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        // CartService should NOT be called if validation fails in controller
        $this->mock(CartService::class, function ($mock) {
            $mock->shouldNotReceive('addToCart');
        });

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 10]);

        // Based on controller logic, it catches BusinessLogicException and redirects back with error
        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function remove_item_calls_service()
    {
        $user = User::factory()->create();
        $this->mock(CartService::class, function ($mock) {
            $mock->shouldReceive('removeFromCart')->once()->with(1);
        });

        $response = $this->actingAs($user)->post(route('cart.remove', 1));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function update_quantity_calls_service_and_returns_json()
    {
        $user = User::factory()->create();
        $this->mock(CartService::class, function ($mock) {
            $mock->shouldReceive('updateQuantity')->once()->with(1, 5);
            $mock->shouldReceive('getCart')->andReturn(collect([
                (object) ['product_id' => 1, 'subtotal' => 50]
            ]));
            $mock->shouldReceive('getTotal')->andReturn(50);
            $mock->shouldReceive('getCount')->andReturn(5);
        });

        $response = $this->actingAs($user)->patch(route('cart.update'), ['product_id' => 1, 'quantity' => 5]);

        $response->assertOk();
    }

    /** @test */
    public function update_returns_0_if_item_not_found()
    {
        $user = User::factory()->create();
        $this->mock(CartService::class, function ($mock) {
            $mock->shouldReceive('updateQuantity');
            $mock->shouldReceive('getCart')->andReturn(collect([])); // Empty cart
            $mock->shouldReceive('getTotal')->andReturn(0);
            $mock->shouldReceive('getCount')->andReturn(0);
        });

        $response = $this->actingAs($user)->patch(route('cart.update'), ['product_id' => 999, 'quantity' => 5]);

        $response->assertOk();
        $response->assertJson(['itemTotal' => '0.00']);
    }

    /** @test */
    public function can_view_login_required_message()
    {
        $response = $this->get(route('cart.login-required'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.cart-login-required');
    }
}

