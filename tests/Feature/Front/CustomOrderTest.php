<?php

namespace Tests\Feature\Front;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_view_custom_order_page()
    {
        $response = $this->get(route('custom-order.create'));

        $response->assertOk();
        $response->assertViewIs('front.custom-order');
    }

    /** @test */
    public function guest_can_store_custom_order()
    {
        Storage::fake('public');

        $data = [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '0987654321',
            'description' => 'I want a custom amigurumi dragon with specific colors.',
            'suggested_date' => now()->addDays(15)->format('Y-m-d'),
            'images' => [
                UploadedFile::fake()->image('dragon1.jpg'),
            ],
        ];

        $response = $this->post(route('custom-order.store'), $data);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'guest@example.com',
            'type' => 'custom',
            'status' => Order::STATUS_QUOTATION,
        ]);

        $order = Order::where('customer_email', 'guest@example.com')->first();
        $this->assertCount(1, $order->items);
        $this->assertNotNull($order->items->first()->images);
    }

    /** @test */
    public function authenticated_user_can_store_custom_order_and_is_linked()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $data = [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0987654321',
            'description' => 'I want a custom amigurumi dragon with specific colors.',
        ];

        $response = $this->actingAs($user)->post(route('custom-order.store'), $data);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'customer_email' => $user->email,
        ]);
    }

    /** @test */
    public function custom_order_validation_fails_with_short_description()
    {
        $data = [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '0987654321',
            'description' => 'Too short',
        ];

        $response = $this->from(route('custom-order.create'))->post(route('custom-order.store'), $data);

        $response->assertRedirect(route('custom-order.create'));
        $response->assertSessionHasErrors(['description']);
    }
}
