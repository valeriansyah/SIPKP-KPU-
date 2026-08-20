<?php

namespace App\Http\Controllers;

use App\Services\OTPService;
use App\Services\AuthService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    protected $otpService;
    protected $authService;

    public function __construct(OTPService $otpService, AuthService $authService)
    {
        $this->otpService = $otpService;
        $this->authService = $authService;
    }

    public function showRegistrationForm()
    {
        return response()->json(['message' => 'Show registration form']);
    }

    public function sendRegistrationOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $key = 'send-otp:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'errors' => ['email' => ['Too many attempts. Please try again later.']]
            ], 422);
        }
        RateLimiter::hit($key, 60);

        $request->validate(['email' => 'required|email|unique:users,email']);
        
        $this->otpService->generateRegistrationOTP($request->email);
        
        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyRegistrationOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $key = 'verify-otp:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many verification attempts. Please request a new OTP.',
                'errors' => ['otp' => ['Too many verification attempts. Please request a new OTP.']]
            ], 422);
        }
        RateLimiter::hit($key, 60);

        $isValid = $this->otpService->verifyRegistrationOTP($request->email, $request->otp);

        if ($isValid) {
            RateLimiter::clear($key);
            $request->session()->put('verified_registration_email', $request->email);
            
            AuditLog::create([
                'user_id' => null,
                'activity' => 'OTP Verification',
                'description' => 'OTP registration verified successfully for ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['message' => 'OTP verified successfully']);
        }

        return response()->json([
            'message' => 'The provided OTP is invalid or expired.',
            'errors' => ['otp' => ['The provided OTP is invalid or expired.']]
        ], 422);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|min:5|max:30|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'password' => 'required|string|min:8|max:20|regex:/[a-zA-Z]/|regex:/[0-9]/',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($request->session()->get('verified_registration_email') !== $request->email) {
            return response()->json([
                'message' => 'Email has not been verified via OTP.',
                'errors' => ['email' => ['Email has not been verified via OTP.']]
            ], 422);
        }

        $user = $this->authService->registerUser($validated);
        
        $request->session()->forget('verified_registration_email');

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);
    }

    public function showLoginForm(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Show login form']);
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = 'login:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Too many login attempts. Please try again later.',
                    'errors' => ['email' => ['Too many login attempts. Please try again later.']]
                ], 422);
            }
            return back()->withErrors(['email' => 'Terlalu banyak percobaan masuk. Silakan coba beberapa saat lagi.'])->withInput();
        }

        if ($this->authService->login($request->email, $request->password)) {
            RateLimiter::clear($key);
            
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Login successful']);
            }
            
            $user = \Illuminate\Support\Facades\Auth::user();
            $roleSlug = \Illuminate\Support\Str::slug($user->role->role_name, '_');
            
            if ($roleSlug === 'pelapor') {
                return redirect()->intended(route('pelapor.dashboard'));
            } elseif ($roleSlug === 'sub_operator') {
                return redirect()->intended(route('sub_operator.dashboard'));
            } elseif ($roleSlug === 'operator_provinsi') {
                return redirect()->intended(route('operator.dashboard'));
            }
            
            \Illuminate\Support\Facades\Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akses ditolak. Peran tidak dikenali.']);
        }

        RateLimiter::hit($key, 60);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'The provided credentials do not match our records or the account is inactive.',
                'errors' => ['email' => ['The provided credentials do not match our records or the account is inactive.']]
            ], 422);
        }
        
        return back()->withErrors(['email' => 'Kredensial yang diberikan tidak cocok dengan data kami atau akun tidak aktif.'])->withInput();
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }
        
        return redirect()->route('login');
    }

    public function sendResetOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $key = 'send-reset-otp:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'errors' => ['email' => ['Too many attempts. Please try again later.']]
            ], 422);
        }
        RateLimiter::hit($key, 60);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and is active and not deleted
        if ($user && $user->is_active) {
            $this->otpService->generateResetPasswordOTP($user->id, $user->email);
        }

        // Return a generic message regardless of user existence to prevent email enumeration
        return response()->json(['message' => 'If your email is registered and active, an OTP has been sent.']);
    }

    public function verifyResetOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $key = 'verify-reset-otp:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many verification attempts. Please request a new OTP.',
                'errors' => ['otp' => ['Too many verification attempts. Please request a new OTP.']]
            ], 422);
        }
        RateLimiter::hit($key, 60);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_active || !$this->otpService->verifyResetPasswordOTP($request->email, $request->otp)) {
            return response()->json([
                'message' => 'The provided OTP is invalid or expired.',
                'errors' => ['otp' => ['The provided OTP is invalid or expired.']]
            ], 422);
        }

        RateLimiter::clear($key);
        $request->session()->put('verified_reset_email', $request->email);

        AuditLog::create([
            'user_id' => $user->id,
            'activity' => 'OTP Verification',
            'description' => 'Password reset OTP verified successfully',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'OTP verified successfully. You can now reset your password.']);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:20|regex:/[a-zA-Z]/|regex:/[0-9]/',
        ]);

        if ($request->session()->get('verified_reset_email') !== $request->email) {
            return response()->json([
                'message' => 'Email has not been verified via OTP.',
                'errors' => ['email' => ['Email has not been verified via OTP.']]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_active) {
            return response()->json([
                'message' => 'User not found or inactive.',
                'errors' => ['email' => ['User not found or inactive.']]
            ], 422);
        }

        $this->authService->resetPassword($user, $validated['password']);
        
        $request->session()->forget('verified_reset_email');

        return response()->json(['message' => 'Password reset successfully']);
    }
}
