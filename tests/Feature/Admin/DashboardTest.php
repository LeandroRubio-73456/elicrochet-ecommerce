<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Setup data for KPIs
        \App\Models\Order::factory()->create(['status' => 'paid', 'total_amount' => 500]);
        \App\Models\Product::factory()->create(['stock' => 2]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('back.dashboard');
        $response->assertViewHas(['totalUsers', 'totalOrders', 'totalSales', 'recentOrders', 'lowStockProducts']);
    }
}
