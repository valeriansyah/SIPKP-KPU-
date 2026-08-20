<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use App\Models\District;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutVite();
        
        // Ensure roles exist
        Role::firstOrCreate(['role_name' => 'Pelapor']);
        Role::firstOrCreate(['role_name' => 'Sub Operator']);
        Role::firstOrCreate(['role_name' => 'Operator Provinsi']);
    }

    public function test_web_login_form_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_sub_operator_login_success_and_district_isolation()
    {
        $role = Role::where('role_name', 'Sub Operator')->first();
        $district = District::firstOrCreate(['name' => 'Lahat'], ['code' => '16.04']);

        $user = User::create([
            'full_name' => 'SubOp Lahat',
            'username' => 'subop_lahat',
            'phone_number' => '08123456789',
            'email' => 'subop.lahat@sipkp.local',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => $district->id,
            'is_active' => true
        ]);

        // Test API flow (json)
        $response = $this->postJson('/login', [
            'email' => 'subop.lahat@sipkp.local',
            'password' => 'Password123'
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'activity' => 'Login'
        ]);
        
        $this->postJson('/logout');
        $this->assertGuest();
        
        // Test Web flow (redirect)
        $response = $this->post('/login', [
            'email' => 'subop.lahat@sipkp.local',
            'password' => 'Password123'
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('sub_operator.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_operator_provinsi_login_success_and_global_access()
    {
        $role = Role::where('role_name', 'Operator Provinsi')->first();

        $user = User::create([
            'full_name' => 'Operator Provinsi',
            'username' => 'operator_provinsi',
            'phone_number' => '08123456789',
            'email' => 'operator@sipkp.local',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => null, // Operator can see all districts
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => 'operator@sipkp.local',
            'password' => 'Password123'
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('operator.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_wrong_password()
    {
        $role = Role::where('role_name', 'Sub Operator')->first();

        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser2',
            'phone_number' => '08123456789',
            'email' => 'wrong@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => true
        ]);

        $response = $this->postJson('/login', [
            'email' => 'wrong@example.com',
            'password' => 'WrongPassword'
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
        
        // Web flow
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'WrongPassword'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function test_login_inactive_account()
    {
        $role = Role::where('role_name', 'Sub Operator')->first();

        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser3',
            'phone_number' => '08123456789',
            'email' => 'inactive@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => false
        ]);

        $response = $this->postJson('/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password123'
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    public function test_login_soft_deleted_account()
    {
        $role = Role::where('role_name', 'Sub Operator')->first();

        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser4',
            'phone_number' => '08123456789',
            'email' => 'deleted@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        $user->delete();

        $response = $this->postJson('/login', [
            'email' => 'deleted@example.com',
            'password' => 'Password123'
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    public function test_login_rate_limiting()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', [
                'email' => 'brute@example.com',
                'password' => 'wrong'
            ]);
        }

        $response = $this->postJson('/login', [
            'email' => 'brute@example.com',
            'password' => 'wrong'
        ]);

        $response->assertStatus(422);
    }

    public function test_logout_and_audit_log()
    {
        $role = Role::where('role_name', 'Sub Operator')->first();

        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser5',
            'phone_number' => '08123456789',
            'email' => 'logout@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        $this->actingAs($user);

        // API
        $response = $this->postJson('/logout');
        $response->assertStatus(200);
        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'activity' => 'Logout'
        ]);
        
        // Web
        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
