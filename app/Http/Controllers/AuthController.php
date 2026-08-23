<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
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

        $key = 'login:'.$request->ip().':'.$request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Too many login attempts. Please try again later.',
                    'errors' => ['email' => ['Too many login attempts. Please try again later.']],
                ], 422);
            }

            return back()->withErrors(['email' => 'Terlalu banyak percobaan masuk. Silakan coba beberapa saat lagi.'])->withInput();
        }

        if ($this->authService->login($request->email, $request->password)) {
            RateLimiter::clear($key);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Login successful']);
            }

            $user = Auth::user();
            $roleSlug = Str::slug($user->role->role_name, '_');

            if ($roleSlug === 'pelapor') {
                return redirect()->route('pelapor.dashboard');
            } elseif ($roleSlug === 'sub_operator') {
                return redirect()->route('sub_operator.dashboard');
            } elseif ($roleSlug === 'operator_provinsi') {
                return redirect()->route('operator.dashboard');
            }

            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Akses ditolak. Peran tidak dikenali.']);
        }

        RateLimiter::hit($key, 60);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'The provided credentials do not match our records or the account is inactive.',
                'errors' => ['email' => ['The provided credentials do not match our records or the account is inactive.']],
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
}
