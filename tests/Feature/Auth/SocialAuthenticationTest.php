<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_redirects_to_google()
    {
        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    /** @test */
    public function it_creates_new_user_from_google_callback()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = '1234567890';
        $abstractUser->name = 'Google User';
        $abstractUser->email = 'google@example.com';
        $abstractUser->avatar = 'https://avatar.com/pic.jpg';

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('auth/google/callback');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google@example.com',
            'google_id' => '1234567890',
            'avatar' => 'https://avatar.com/pic.jpg',
        ]);

        $response->assertRedirect(route('account.index'));
    }

    /** @test */
    public function it_logs_in_existing_user_via_google()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => '987654321',
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = '987654321';
        $abstractUser->name = 'Existing User';
        $abstractUser->email = 'existing@example.com';
        $abstractUser->avatar = 'https://avatar.com/new_pic.jpg';

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('account.index'));
    }

    /** @test */
    public function it_links_google_account_by_email_if_user_exists_but_no_google_id()
    {
        $user = User::factory()->create([
            'email' => 'legacy@example.com',
            'google_id' => null,
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = '111222333';
        $abstractUser->name = 'Legacy User';
        $abstractUser->email = 'legacy@example.com';
        $abstractUser->avatar = 'https://avatar.com/legacy.jpg';

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => '111222333',
            'avatar' => 'https://avatar.com/legacy.jpg',
        ]);
    }

    /** @test */
    public function it_redirects_admin_users_to_admin_dashboard()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = 'ADMIN123';
        $abstractUser->name = 'Admin User';
        $abstractUser->email = 'admin@elicrochet.com';
        $abstractUser->avatar = 'avatar';

        // Pre-create admin user
        User::factory()->create([
            'email' => 'admin@elicrochet.com',
            'role' => 'admin',
            'google_id' => 'ADMIN123',
        ]);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('auth/google/callback');

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function it_handles_callback_exceptions_gracefully()
    {
        Socialite::shouldReceive('driver')->with('google')->andThrow(new \Exception('OAuth Error'));

        $response = $this->get('auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }
}
