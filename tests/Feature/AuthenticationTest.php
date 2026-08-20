<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\OtpCode;
use App\Services\OTPService;
use App\Models\AuditLog;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutVite();
        
        // Ensure roles exist
        Role::create(['role_name' => 'Pelapor']);
        Role::create(['role_name' => 'Sub Operator']);
    }

    public function test_registration_email_submission_and_otp_generation()
    {
        $response = $this->postJson('/register/send-otp', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('otp_codes', [
            'email' => 'test@example.com',
            'purpose' => 'registration'
        ]);
        
        $otpCode = OtpCode::where('email', 'test@example.com')->first();
        $this->assertNotNull($otpCode->otp);
        $this->assertTrue(now()->diffInMinutes($otpCode->expired_at) <= 5);
        $this->assertNull($otpCode->user_id);
    }

    public function test_old_otp_invalidated_after_resend()
    {
        $this->postJson('/register/send-otp', ['email' => 'test@example.com']);
        $firstOtp = OtpCode::first();
        $this->assertTrue($firstOtp->expired_at > now());

        $this->postJson('/register/send-otp', ['email' => 'test@example.com']);
        
        $firstOtp->refresh();
        $this->assertTrue($firstOtp->expired_at <= now()); // Invalidated

        $this->assertEquals(2, OtpCode::count());
    }

    public function test_otp_resend_rate_limiting()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/register/send-otp', ['email' => 'limit@example.com']);
        }
        
        // 4th attempt should hit rate limit
        $response = $this->postJson('/register/send-otp', ['email' => 'limit@example.com']);
        $response->assertStatus(422);
    }

    public function test_otp_verification_success()
    {
        // Mock OTPService to return known OTP or we can just mock the random generator
        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        $this->postJson('/register/send-otp', ['email' => 'verify@example.com']);

        $response = $this->postJson('/register/verify-otp', [
            'email' => 'verify@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(200);
        $response->assertSessionHas('verified_registration_email', 'verify@example.com');
        
        $this->assertDatabaseHas('otp_codes', [
            'email' => 'verify@example.com',
        ]);
        $otpRecord = OtpCode::where('email', 'verify@example.com')->first();
        $this->assertNotNull($otpRecord->verified_at);

        // Audit Log check
        $this->assertDatabaseHas('audit_logs', [
            'activity' => 'OTP Verification',
            'user_id' => null
        ]);
    }

    public function test_otp_verification_failure_and_expiration()
    {
        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        $this->postJson('/register/send-otp', ['email' => 'fail@example.com']);

        // Wrong OTP
        $response = $this->postJson('/register/verify-otp', [
            'email' => 'fail@example.com',
            'otp' => '654321'
        ]);

        $response->assertStatus(422);
        
        // Simulate expiration
        OtpCode::where('email', 'fail@example.com')->update(['expired_at' => now()->subMinute()]);
        
        $response = $this->postJson('/register/verify-otp', [
            'email' => 'fail@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(422);
    }

    public function test_user_creation_after_successful_otp_and_role_assignment()
    {
        $this->withSession(['verified_registration_email' => 'newuser@example.com']);

        $response = $this->postJson('/register', [
            'email' => 'newuser@example.com',
            'full_name' => 'New User',
            'username' => 'new_user',
            'password' => 'Password123',
            'phone_number' => '08123456789'
        ]);

        $response->assertStatus(201);
        
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Pelapor', $user->role->role_name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->district_id);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'activity' => 'Registrasi'
        ]);
    }

    public function test_registration_fails_without_verified_email()
    {
        $response = $this->postJson('/register', [
            'email' => 'unverified@example.com',
            'full_name' => 'New User',
            'username' => 'unverified_user',
            'password' => 'Password123',
            'phone_number' => '08123456789'
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }

    public function test_web_login_form_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_login_success_and_session_regeneration()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'login@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        // Test API flow (json)
        $response = $this->postJson('/login', [
            'email' => 'login@example.com',
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
            'email' => 'login@example.com',
            'password' => 'Password123'
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('pelapor.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_wrong_password()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser2',
            'phone_number' => '08123456789',
            'email' => 'wrong@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
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
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser3',
            'phone_number' => '08123456789',
            'email' => 'inactive@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
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
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser4',
            'phone_number' => '08123456789',
            'email' => 'deleted@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
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
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser5',
            'phone_number' => '08123456789',
            'email' => 'logout@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
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
