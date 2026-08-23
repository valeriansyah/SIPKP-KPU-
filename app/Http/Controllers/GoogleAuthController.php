<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        $driver = Socialite::driver('google');
        if (app()->environment('local')) {
            $driver->setHttpClient(new Client(['verify' => false]));
        }

        return $driver->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $driver = Socialite::driver('google');
            if (app()->environment('local')) {
                $driver->setHttpClient(new Client(['verify' => false]));
            }
            $googleUser = $driver->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                // Determine Pelapor Role ID
                $pelaporRole = Role::where('role_name', 'Pelapor')->first();
                if (! $pelaporRole) {
                    return redirect()->route('login')->withErrors(['email' => 'Role Pelapor tidak ditemukan di sistem.']);
                }

                $username = 'pelapor_'.strtolower(Str::random(6));

                // Create new user using the Google details
                $user = User::create([
                    'full_name' => $googleUser->getName() ?? 'Pengguna Google',
                    'email' => $googleUser->getEmail(),
                    'username' => $username,
                    'phone_number' => '-', // Dummy, to be updated in profile
                    'password' => Hash::make(Str::random(24)), // Random dummy password
                    'role_id' => $pelaporRole->id,
                    'district_id' => null,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'profile_picture' => $googleUser->getAvatar(),
                ]);
            } else {
                // If user already exists but their profile picture is not set, optionally update it
                if (empty($user->profile_picture)) {
                    $user->update([
                        'profile_picture' => $googleUser->getAvatar(),
                    ]);
                }
            }

            // Ensure the user actually has the 'pelapor' role if trying to access public system,
            // but if they are an internal operator/subop with matching email, the system shouldn't block them entirely.
            // Wait, we should block operators from logging in via Google to enforce security policies.
            // So we check if role is Pelapor.
            if ($user->role->role_name !== 'Pelapor') {
                return redirect()->route('login')->withErrors(['email' => 'Akun Anda merupakan akun internal. Harap gunakan form login utama.']);
            }

            // Log them in securely
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('pelapor.dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Autentikasi Google gagal. Silakan coba lagi.']);
        }
    }
}
