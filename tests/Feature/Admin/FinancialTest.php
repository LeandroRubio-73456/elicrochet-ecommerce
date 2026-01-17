<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_financial_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get(route('admin.finance.index'));

        $response->assertOk();
        $response->assertViewIs('back.finance.index');
        $response->assertViewHasAll(['totalIncome', 'avgTicket', 'totalProductsSold', 'conversionRate']);
    }

    /** @test */
    public function financial_dashboard_calculates_kpis_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create orders
        Order::factory()->create(['total_amount' => 100, 'status' => Order::STATUS_PAID, 'created_at' => Carbon::now()]);
        Order::factory()->create(['total_amount' => 50, 'status' => Order::STATUS_PAID, 'created_at' => Carbon::now()]);
        
        $response = $this->actingAs($admin)->get(route('admin.finance.index', ['period' => 'this_month']));

        $response->assertOk();
        // Total Income should be 150
        $response->assertViewHas('totalIncome', 150);
        // Avg Ticket should be 75
        $response->assertViewHas('avgTicket', 75);
    }

    /** @test */
    public function admin_can_export_financial_report()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create order to be exported
        $order = Order::factory()->create([
            'total_amount' => 123.45, 
            'status' => Order::STATUS_PAID, 
            'customer_name' => 'Export Customer'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.finance.export', ['period' => 'this_month']));

        $response->assertOk();
        $response->assertHeader('Content-type', 'text/csv; charset=UTF-8');
        
        // Verify content stream
        $content = $response->streamedContent();
        $this->assertStringContainsString('Reporte de Ventas - EliCrochet', $content);
        $this->assertStringContainsString('Export Customer', $content);
        $this->assertStringContainsString('123,45', $content); // Check number format
    }

    /** @test */
    public function financial_dashboard_applies_date_filters()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Old order
        Order::factory()->create([
            'total_amount' => 500, 
            'status' => Order::STATUS_PAID, 
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        $response = $this->actingAs($admin)->get(route('admin.finance.index', ['period' => 'this_month']));

        // Should not include the old order
        $response->assertViewHas('totalIncome', 0);
    }
}
