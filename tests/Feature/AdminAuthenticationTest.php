<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in(): void
    {
        $user = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'kata-laluan-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_guests_cannot_access_admin_pages(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.weddings.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.weddings.create'))->assertRedirect(route('admin.login'));
    }

    public function test_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login.store'), [
                'email' => 'pentadbir@example.com',
                'password' => 'salah-sekali',
            ])->assertStatus(302);
        }

        $this->post(route('admin.login.store'), [
            'email' => 'pentadbir@example.com',
            'password' => 'salah-sekali',
        ])->assertStatus(429);
    }
}
