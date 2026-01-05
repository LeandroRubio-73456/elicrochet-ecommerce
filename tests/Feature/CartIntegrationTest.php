<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_add_to_cart_full_flow()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 50]);

        $response = $this->actingAs($user)->post(route('cart.add', $product), [
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function user_cannot_add_more_than_stock_full_flow()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'price' => 50]);

        // Try to add 6
        $response = $this->actingAs($user)->post(route('cart.add', $product), [
            'quantity' => 6,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }

    /** @test */
    public function user_can_update_cart_quantity_ajax()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 50]);

        // Add initial item
        $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 1]);

        // Update via AJAX
        $response = $this->actingAs($user)->patch(route('cart.update'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function user_can_remove_item_full_flow()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 1]);

        $response = $this->actingAs($user)->post(route('cart.remove', $product->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }
}
