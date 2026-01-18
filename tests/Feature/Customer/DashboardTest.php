<?php

namespace Tests\Feature\Customer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_can_access_customer_dashboard()
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('account.index'));

        $response->assertOk();
        $response->assertViewIs('front.account.index');
        $response->assertViewHas('recentOrders');
    }

    /** @test */
    public function guest_cannot_access_customer_dashboard()
    {
        $this->get(route('account.index'))->assertRedirect(route('login'));
    }
}
