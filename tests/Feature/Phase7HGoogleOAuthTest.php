<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;
use Mockery;

class Phase7HGoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist
        Role::firstOrCreate(['role_name' => 'Pelapor']);
        Role::firstOrCreate(['role_name' => 'Sub Operator']);
    }

    public function test_google_redirect()
    {
        $response = $this->get('/auth/google/redirect');
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_pelapor_account_creation_via_google_callback()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('123456789')
            ->shouldReceive('getName')->andReturn('Google User')
            ->shouldReceive('getEmail')->andReturn('google@example.com')
            ->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        Socialite::shouldReceive('driver')->with('google')->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($abstractUser)->getMock());

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('pelapor.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'google@example.com',
            'full_name' => 'Google User',
            'phone_number' => '-',
        ]);

        $user = User::where('email', 'google@example.com')->first();
        $this->assertEquals('Pelapor', $user->role->role_name);
    }

    public function test_pelapor_login_kembali_via_google_callback()
    {
        $role = Role::where('role_name', 'Pelapor')->first();
        User::create([
            'full_name' => 'Existing User',
            'email' => 'existing@example.com',
            'username' => 'existing_user',
            'phone_number' => '08123456789',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('987654321')
            ->shouldReceive('getName')->andReturn('Existing User')
            ->shouldReceive('getEmail')->andReturn('existing@example.com')
            ->shouldReceive('getAvatar')->andReturn('https://new-avatar.url');

        Socialite::shouldReceive('driver')->with('google')->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($abstractUser)->getMock());

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('pelapor.dashboard'));
        $this->assertAuthenticated();
        
        $this->assertDatabaseCount('users', 1);
    }

    public function test_sub_operator_cannot_login_via_google()
    {
        $role = Role::where('role_name', 'Sub Operator')->first();
        User::create([
            'full_name' => 'Sub Op',
            'email' => 'subop@example.com',
            'username' => 'subop_user',
            'phone_number' => '0811111111',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('111111111')
            ->shouldReceive('getName')->andReturn('Sub Op')
            ->shouldReceive('getEmail')->andReturn('subop@example.com')
            ->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        Socialite::shouldReceive('driver')->with('google')->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($abstractUser)->getMock());

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
