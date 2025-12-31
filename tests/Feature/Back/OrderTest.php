<?php

namespace Tests\Feature\Back;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Assuming admin middleware checks for a role or permission
        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_view_orders_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('back.orders.index');
    }

    /** @test */
    public function index_returns_json_for_datatables()
    {
        Order::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['ajax' => true]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
    }

    /** @test */
    public function admin_can_view_order_details()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('back.orders.show');
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        Mail::fake();
        $order = Order::factory()->create(['status' => 'pending_payment']);

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'status' => 'paid',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
    }

    /** @test */
    public function updating_quotation_amount_updates_item_price()
    {
        $order = Order::factory()->create(['type' => 'custom', 'status' => 'quotation']);
        // Create a custom item linked to this order
        $order->items()->create([
             'product_id' => null,
             'price' => 0,
             'quantity' => 1
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'status' => 'quotation',
            'total_amount' => 150.00
        ]);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total_amount' => 150.00]);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'price' => 150.00]);
    }

    /** @test */
    public function sends_email_when_status_changes_to_pending_payment()
    {
        Mail::fake();
        $order = Order::factory()->create(['type' => 'custom', 'status' => 'quotation']);

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'status' => 'pending_payment',
            'total_amount' => 100 // Required validation
        ]);

        Mail::assertSent(\App\Mail\PriceAssignedNotification::class);
    }
}
