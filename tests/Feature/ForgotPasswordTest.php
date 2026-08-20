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

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::firstOrCreate(['role_name' => 'Pelapor']);
    }

    public function test_forgot_password_request_success()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'reset@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $response = $this->postJson('/forgot-password/send-otp', [
            'email' => 'reset@example.com'
        ]);

        $response->assertStatus(200);
        
        $otpCode = OtpCode::where('email', 'reset@example.com')->first();
        $this->assertNotNull($otpCode);
        $this->assertEquals('reset_password', $otpCode->purpose);
        $this->assertEquals($user->id, $otpCode->user_id);
    }

    public function test_reset_otp_verification_success()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'resetverify@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        $this->postJson('/forgot-password/send-otp', ['email' => 'resetverify@example.com']);

        $response = $this->postJson('/forgot-password/verify-otp', [
            'email' => 'resetverify@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(200);
        $response->assertSessionHas('verified_reset_email', 'resetverify@example.com');
        
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'activity' => 'OTP Verification',
        ]);
    }

    public function test_reset_otp_expired_rejected()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'expired@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        $this->postJson('/forgot-password/send-otp', ['email' => 'expired@example.com']);

        OtpCode::where('email', 'expired@example.com')->update(['expired_at' => now()->subMinute()]);

        $response = $this->postJson('/forgot-password/verify-otp', [
            'email' => 'expired@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(422);
    }

    public function test_reset_otp_wrong_rejected()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'wrongotp@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        $this->postJson('/forgot-password/send-otp', ['email' => 'wrongotp@example.com']);

        $response = $this->postJson('/forgot-password/verify-otp', [
            'email' => 'wrongotp@example.com',
            'otp' => '654321'
        ]);

        $response->assertStatus(422);
    }

    public function test_registration_otp_cannot_be_used_for_reset()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'crosspropose@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        // Send registration OTP
        $this->postJson('/register/send-otp', ['email' => 'crosspropose@example.com']);

        // Try to verify it as reset OTP
        $response = $this->postJson('/forgot-password/verify-otp', [
            'email' => 'crosspropose@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(422);
    }
    
    public function test_reset_otp_cannot_be_used_for_registration()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'crosspropose2@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $otpService = \Mockery::mock(OTPService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $otpService->shouldReceive('generateRandomOTP')->andReturn('123456');
        $this->app->instance(OTPService::class, $otpService);

        // Send reset OTP
        $this->postJson('/forgot-password/send-otp', ['email' => 'crosspropose2@example.com']);

        // Try to verify it as registration OTP
        $response = $this->postJson('/register/verify-otp', [
            'email' => 'crosspropose2@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(422);
    }

    public function test_old_reset_otp_invalidated_after_resend()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'resend@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $this->postJson('/forgot-password/send-otp', ['email' => 'resend@example.com']);
        $firstOtp = OtpCode::first();
        $this->assertTrue($firstOtp->expired_at > now());

        $this->postJson('/forgot-password/send-otp', ['email' => 'resend@example.com']);
        
        $firstOtp->refresh();
        $this->assertTrue($firstOtp->expired_at <= now()); // Invalidated
    }

    public function test_reset_otp_rate_limiting()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/forgot-password/send-otp', ['email' => 'ratelimit@example.com']);
        }
        
        $response = $this->postJson('/forgot-password/send-otp', ['email' => 'ratelimit@example.com']);
        $response->assertStatus(422);
    }

    public function test_soft_deleted_user_cannot_request_reset()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'deleted@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);
        $user->delete();

        $response = $this->postJson('/forgot-password/send-otp', [
            'email' => 'deleted@example.com'
        ]);

        // It returns a generic 200 message but shouldn't create OTP
        $response->assertStatus(200);
        $this->assertDatabaseMissing('otp_codes', ['email' => 'deleted@example.com']);
    }

    public function test_inactive_user_cannot_request_reset()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'inactive@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => false
        ]);

        $response = $this->postJson('/forgot-password/send-otp', [
            'email' => 'inactive@example.com'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('otp_codes', ['email' => 'inactive@example.com']);
    }

    public function test_password_reset_fails_if_otp_not_verified()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'nootp@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $response = $this->postJson('/forgot-password/reset', [
            'email' => 'nootp@example.com',
            'password' => 'NewPassword123'
        ]);

        $response->assertStatus(422);
    }

    public function test_password_reset_success_after_otp()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'resetok@example.com',
            'password' => Hash::make('OldPassword123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $this->withSession(['verified_reset_email' => 'resetok@example.com']);

        $response = $this->postJson('/forgot-password/reset', [
            'email' => 'resetok@example.com',
            'password' => 'NewPassword123'
        ]);

        $response->assertStatus(200);
        
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $user->password));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'activity' => 'Reset Password'
        ]);
        
        // Cannot login with old password
        $this->assertFalse(\Auth::attempt(['email' => 'resetok@example.com', 'password' => 'OldPassword123']));
        // Can login with new password
        $this->assertTrue(\Auth::attempt(['email' => 'resetok@example.com', 'password' => 'NewPassword123', 'is_active' => 1]));
    }
    
    public function test_session_reset_password_cannot_be_used_for_other_user()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'phone_number' => '08123456789',
            'email' => 'userA@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true
        ]);

        $this->withSession(['verified_reset_email' => 'userA@example.com']);

        // Try resetting password for user B using user A's session
        $response = $this->postJson('/forgot-password/reset', [
            'email' => 'userB@example.com',
            'password' => 'NewPassword123'
        ]);

        $response->assertStatus(422);
    }
}
