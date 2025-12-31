<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function back_orders_index_works()
    {
        Order::factory()->count(2)->create();
        $response = $this->actingAs($this->admin)->get(route('admin.back.orders.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.back.orders.index', ['ajax' => 1]), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function back_orders_show_works()
    {
        $order = Order::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('admin.back.orders.show', $order));
        $response->assertStatus(200);
    }

    /** @test */
    public function back_orders_update_works()
    {
        $order = Order::factory()->create(['status' => 'pending_payment']);
        
        $response = $this->actingAs($this->admin)->put(route('admin.back.orders.update', $order), [
            'status' => 'paid'
        ]);

        $response->assertRedirect();
        $this->assertEquals('paid', $order->fresh()->status);
    }

    /** @test */
    public function back_orders_update_fails_on_processing_to_cancelled()
    {
        // Line 63 check
        $order = Order::factory()->create(['status' => 'processing']);
        
        $response = $this->actingAs($this->admin)->put(route('admin.back.orders.update', $order), [
            'status' => 'cancelled'
        ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function back_orders_update_requires_amount_for_custom_pending()
    {
        // Line 54 check
        $order = Order::factory()->create(['type' => 'custom', 'status' => 'quotation']);
        
        $response = $this->actingAs($this->admin)->put(route('admin.back.orders.update', $order), [
            'status' => 'pending_payment'
        ]);

        $response->assertSessionHasErrors('total_amount');
    }
}
