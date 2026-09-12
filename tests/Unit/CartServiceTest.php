<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService;
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_add_item_to_cart()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $result = $this->cartService->addToCart($product, 2);

        $this->assertTrue($result);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function it_updates_quantity_if_item_exists()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $this->cartService->addToCart($product, 1);
        $this->cartService->addToCart($product, 2);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 3, // 1 + 2
        ]);
    }

    /** @test */
    public function it_prevents_adding_more_than_stock()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create(['price' => 100, 'stock' => 5]);

        $this->expectException(\App\Exceptions\BusinessLogicException::class);

        $this->cartService->addToCart($product, 6);
    }

    /** @test */
    public function it_can_remove_item_from_cart()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create(['stock' => 10]);
        $this->cartService->addToCart($product, 1);

        $this->assertCount(1, $this->cartService->getCart());

        $result = $this->cartService->removeFromCart($product->id);

        $this->assertTrue($result);
        $this->assertCount(0, $this->cartService->getCart());
    }

    /** @test */
    public function it_can_get_cart_total()
    {
        $this->actingAs($this->user);
        $product1 = Product::factory()->create(['price' => 100, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 50, 'stock' => 10]);

        $this->cartService->addToCart($product1, 2); // 200
        $this->cartService->addToCart($product2, 1); // 50

        $this->assertEquals(250, $this->cartService->getTotal());
    }

    /** @test */
    public function it_can_clear_cart()
    {
        $this->actingAs($this->user);
        $product = Product::factory()->create(['stock' => 10]);
        $this->cartService->addToCart($product, 1);

        $this->cartService->clearCart();

        $this->assertCount(0, $this->cartService->getCart());
    }

    /** @test */
    public function it_can_add_custom_order_to_cart()
    {
        $this->actingAs($this->user);
        $order = \App\Models\Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => \App\Models\Order::STATUS_PENDING_PAYMENT,
            'total_amount' => 50.00,
            'type' => \App\Models\Order::TYPE_CUSTOM,
        ]);

        $result = $this->cartService->addCustomOrder($order);

        $this->assertTrue($result);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'custom_order_id' => $order->id,
            'price' => 50.00,
        ]);

        $this->assertEquals(\App\Models\Order::STATUS_IN_CART, $order->fresh()->status);
    }

    /** @test */
    public function it_prevents_adding_duplicate_custom_order()
    {
        $this->actingAs($this->user);
        $order = \App\Models\Order::factory()->create([
            'user_id' => $this->user->id,
            'type' => \App\Models\Order::TYPE_CUSTOM,
        ]);

        $this->cartService->addCustomOrder($order);

        $this->expectException(\App\Exceptions\BusinessLogicException::class);
        $this->cartService->addCustomOrder($order);
    }

    /** @test */
    public function it_reverts_order_status_when_removing_from_cart()
    {
        $this->actingAs($this->user);
        $order = \App\Models\Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => \App\Models\Order::STATUS_PENDING_PAYMENT,
            'type' => \App\Models\Order::TYPE_CUSTOM,
        ]);

        $this->cartService->addCustomOrder($order);
        $this->assertEquals(\App\Models\Order::STATUS_IN_CART, $order->fresh()->status);

        $this->cartService->removeFromCart($order->id);

        $this->assertEquals(\App\Models\Order::STATUS_PENDING_PAYMENT, $order->fresh()->status);
    }
}
