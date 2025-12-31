<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_view_finance_index()
    {
        Order::factory()->count(5)->create(['status' => 'paid']);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.index'));

        $response->assertStatus(200);
        $response->assertViewIs('back.finance.index');
        $response->assertViewHas(['totalIncome', 'paidCount']);
    }

    /** @test */
    public function admin_can_export_finance_csv()
    {
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.finance.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=financial_report_' . date('Y-m-d') . '_' . date('H-i') . '.csv');
    }
}
