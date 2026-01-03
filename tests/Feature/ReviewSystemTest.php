<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_review_route()
    {
        $product = Product::factory()->create();

        $response = $this->post(route('reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great!',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_review_unpurchased_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('reviews.store', $product), [
            'rating' => 5,
            'title' => 'Title',
            'comment' => 'Comment',
        ]);

        // Should return redirect back with error
        $response->assertRedirect(route('product.show', $product->slug));
        $response->assertSessionHas('error', 'Debes haber comprado, recibido y completado la orden de este producto para dejar una reseña.');
        
        $this->assertDatabaseMissing('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_cannot_review_purchased_but_not_completed_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create Order (Paid but not Completed)
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_SHIPPED, // Shipped but not completed
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('reviews.store', $product), [
            'rating' => 5, // Valid rating
            'title' => 'Title',
            'comment' => 'Comment',
        ]);

        $response->assertSessionHas('error', 'Debes haber comprado, recibido y completado la orden de este producto para dejar una reseña.');
    }

    public function test_user_can_review_completed_purchased_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create Completed Order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_COMPLETED,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('reviews.store', $product), [
            'rating' => 5,
            'title' => 'Amazing Product',
            'comment' => 'I loved it!',
        ]);

        $response->assertRedirect(route('product.show', $product->slug));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Amazing Product',
            'is_verified_purchase' => true,
        ]);
    }

    public function test_user_cannot_review_same_product_twice()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Setup: Completed purchase
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_COMPLETED,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        // Create first review
        Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('reviews.store', $product), [
            'rating' => 4,
            'comment' => 'Another review',
        ]);

        // Controller uses back() which might redirect to product page in test env
        // We check for error session
        $response->assertSessionHas('error'); // "Ya has enviado una reseña..." or redirect logic
        
        $this->assertEquals(1, Review::where('user_id', $user->id)->where('product_id', $product->id)->count());
    }
}
