<?php

namespace Tests\Feature\Customer;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function customer_can_list_their_orders()
    {
        // status distinto de pending_payment: una orden en pending_payment
        // de tipo "stock" es un borrador de checkout y el índice la oculta
        // a propósito (ver OrderController::index).
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => Order::STATUS_PAID]);
        $otherOrder = Order::factory()->create(['status' => Order::STATUS_PAID]); // Different user

        $response = $this->actingAs($this->user)->get(route('account.orders.index'));

        $response->assertOk();
        $response->assertViewHas('orders', function ($orders) use ($order, $otherOrder) {
            return $orders->contains($order) && ! $orders->contains($otherOrder);
        });
    }

    /** @test */
    public function customer_can_view_their_order()
    {
        // Igual que en el índice: pending_payment + stock es un borrador de
        // checkout y OrderController::show redirige al carrito en ese caso.
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => Order::STATUS_PAID]);

        $response = $this->actingAs($this->user)->get(route('account.orders.show', $order));

        $response->assertOk();
        $response->assertSee($order->order_number);
    }

    /** @test */
    public function customer_cannot_view_others_order()
    {
        $otherOrder = Order::factory()->create();

        $response = $this->actingAs($this->user)->get(route('account.orders.show', $otherOrder));

        $response->assertStatus(403);
    }

    /** @test */
    public function customer_can_cancel_eligible_order_and_restores_stock()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PAID,
            'type' => Order::TYPE_STOCK,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertEquals(12, $product->fresh()->stock);
    }

    /** @test */
    public function customer_cannot_cancel_ineligible_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_SHIPPED,
            'type' => Order::TYPE_STOCK,
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(Order::STATUS_SHIPPED, $order->fresh()->status);
    }

    /** @test */
    public function customer_can_confirm_receipt()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_SHIPPED,
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.confirm', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(Order::STATUS_COMPLETED, $order->fresh()->status);
    }

    /** @test */
    public function customer_can_view_custom_order_form()
    {
        $response = $this->actingAs($this->user)->get(route('account.custom.create'));

        $response->assertOk();
        $response->assertViewHas('categories');
    }

    /** @test */
    public function customer_can_store_custom_order()
    {
        Mail::fake();
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create([
            'status' => 'active',
            'required_specs' => [['name' => 'Size', 'type' => 'text', 'required' => true]],
        ]);

        $data = [
            'category_id' => $category->id,
            'description' => 'A custom crochet dragon',
            'custom_specs' => ['Size' => 'Large'],
            'images' => [UploadedFile::fake()->image('dragon.jpg')],
        ];

        $response = $this->actingAs($this->user)->post(route('account.custom.store'), $data);

        $response->assertRedirect(route('account.orders.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'type' => Order::TYPE_CUSTOM,
            'status' => Order::STATUS_QUOTATION,
        ]);

        $order = Order::where('user_id', $this->user->id)->where('type', Order::TYPE_CUSTOM)->first();
        $this->assertCount(1, $order->items);
        $this->assertNotNull($order->items->first()->images);

        Mail::assertQueued(\App\Mail\CustomOrderReceived::class);
        Mail::assertQueued(\App\Mail\NewOrderAdminNotification::class);
    }

    /** @test */
    public function customer_can_add_pending_custom_order_to_cart()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'type' => Order::TYPE_CUSTOM,
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.add_to_cart', $order));

        $response->assertRedirect(route('cart'));
        $response->assertSessionHas('success');
        $this->assertEquals(Order::STATUS_IN_CART, $order->fresh()->status);
    }
}
