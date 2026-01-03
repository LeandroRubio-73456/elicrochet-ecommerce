<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_cancel_pending_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING_PAYMENT, // Cancellable status
        ]);

        $this->actingAs($user);

        $response = $this->post(route('customer.orders.cancel', $order));

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
        ]);
    }

    public function test_user_cannot_cancel_shipped_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_SHIPPED, // Not cancellable
        ]);

        $this->actingAs($user);

        $response = $this->post(route('customer.orders.cancel', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_SHIPPED,
        ]);
    }

    public function test_user_can_confirm_receipt_of_shipped_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_SHIPPED,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('customer.orders.confirm', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_COMPLETED,
        ]);
    }

    public function test_user_cannot_confirm_receipt_of_unshipped_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PAID, // Not shipped yet
        ]);

        $this->actingAs($user);

        $response = $this->post(route('customer.orders.confirm', $order));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PAID,
        ]);
    }

    public function test_stock_is_restored_when_cancelling_paid_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PAID,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user);

        // Cancel the order
        $this->post(route('customer.orders.cancel', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
        ]);

        // Stock should be 10 + 2 = 12
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 12,
        ]);
    }
}
