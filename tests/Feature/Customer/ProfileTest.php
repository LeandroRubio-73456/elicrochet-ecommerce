<?php

namespace Tests\Feature\Customer;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function customer_can_view_profile_edit_page()
    {
        $response = $this->actingAs($this->user)->get(route('customer.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.profile.edit');
        $response->assertViewHas('user');
    }

    /** @test */
    public function customer_can_update_profile_information_and_create_address()
    {
        $data = [
            'name' => 'Updated Name',
            'lastname' => 'Updated Lastname',
            'cedula' => '1712345678',
            'email' => 'updated@example.com',
            'phone' => '0999999999',
            'shipping_address' => 'New Street 123',
            'shipping_city' => 'Quito',
            'shipping_province' => 'Pichincha',
            'shipping_reference' => 'Near Park',
            'shipping_zip' => '170102',
        ];

        $response = $this->actingAs($this->user)->put(route('customer.profile.update'), $data);

        $response->assertRedirect(route('customer.profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();

        // Check User fields
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('Updated Lastname', $this->user->lastname);
        $this->assertEquals('1712345678', $this->user->cedula);
        $this->assertEquals('updated@example.com', $this->user->email);
        $this->assertEquals('0999999999', $this->user->phone);

        // Check Address creation/update
        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'street' => 'New Street 123',
            'city' => 'Quito',
            'province' => 'Pichincha',
            'reference' => 'Near Park',
            'postal_code' => '170102',
        ]);
    }

    /** @test */
    /*
    public function customer_can_update_password()
    {
        $data = [
            'name' => $this->user->name,
            'lastname' => 'Lastname',
            'cedula' => '1712345678',
            'email' => 'newemail@example.com', // Change email to avoid unique ignore issues
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->actingAs($this->user)
            ->from(route('customer.profile.edit'))
            ->put(route('customer.profile.update'), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customer.profile.edit'));
        
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $this->user->password));
    }
    */

    /** @test */
    public function customer_cannot_update_with_invalid_data()
    {
         $response = $this->actingAs($this->user)->put(route('customer.profile.update'), [
            'name' => '', // Required
            'email' => 'not-an-email',
            'cedula' => str_repeat('1', 14), // Max 13
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'cedula']);
    }
}
