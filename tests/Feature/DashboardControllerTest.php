<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_access_back_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('back.dashboard'));

        $response->assertOk();
        $response->assertViewIs('back.dashboard');
    }
}
