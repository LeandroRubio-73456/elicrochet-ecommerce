<?php

namespace Tests\Feature\Front;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_access_account_index()
    {
        $response = $this->actingAs($this->user)->get(route('account.index'));

        $response->assertOk();
        $response->assertViewIs('front.account.index');
    }

    /** @test */
    public function user_can_update_profile()
    {
        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '0999999999',
        ];

        $response = $this->actingAs($this->user)->put(route('account.update-profile'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '0999999999',
        ]);
    }

    /** @test */
    public function user_can_update_address()
    {
        $data = [
            'street' => '123 Test St',
            'city' => 'Quito',
            'province' => 'Pichincha',
            'postal_code' => '170101',
            'phone' => '022222222',
            'details' => 'Near the park',
        ];

        $response = $this->actingAs($this->user)->put(route('account.update-address'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'street' => '123 Test St',
            'city' => 'Quito',
        ]);
    }

    /** @test */
    public function user_can_list_orders_via_datatables()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('account.orders'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertJsonStructure(['data', 'draw', 'recordsTotal', 'recordsFiltered']);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function user_can_cancel_eligible_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending_payment',
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    /** @test */
    public function user_cannot_cancel_others_order()
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'pending_payment',
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.cancel', $order));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_confirm_receipt_of_shipped_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'shipped',
        ]);

        $response = $this->actingAs($this->user)->post(route('account.orders.confirm', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('completed', $order->fresh()->status);
    }
}
