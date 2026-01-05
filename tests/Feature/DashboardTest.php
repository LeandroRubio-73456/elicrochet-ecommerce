<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('back.dashboard');
    }

    /** @test */
    public function customer_can_access_customer_dashboard()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get(route('customer.dashboard'));

        $response->assertOk();
        $response->assertViewIs('customer.dashboard');
        $response->assertViewHas('recentOrders');
    }

    /** @test */
    public function guest_cannot_access_dashboards()
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('customer.dashboard'))->assertRedirect(route('login'));
    }
}
