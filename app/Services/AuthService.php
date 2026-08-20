<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function registerUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $pelaporRole = Role::where('role_name', 'Pelapor')->firstOrFail();

            $user = User::create([
                'full_name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'role_id' => $pelaporRole->id,
                'district_id' => null,
                'is_active' => true,
            ]);
            
            // Set email_verified_at explicitly
            $user->email_verified_at = now();
            $user->save();

            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Registrasi',
                'description' => 'User registered successfully',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $user;
        });
    }

    public function login(string $email, string $password): bool
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'is_active' => 1,
        ];
        
        if (Auth::attempt($credentials)) {
            request()->session()->regenerate();
            
            AuditLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Login',
                'description' => 'User logged in successfully',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $userId = Auth::id();
        
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if ($userId) {
            AuditLog::create([
                'user_id' => $userId,
                'activity' => 'Logout',
                'description' => 'User logged out',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        DB::transaction(function () use ($user, $newPassword) {
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Reset Password',
                'description' => 'User password has been reset successfully',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
