<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;

class OTPService
{
    /**
     * Generate a new registration OTP for the given email.
     */
    public function generateRegistrationOTP(string $email): string
    {
        // Invalidate old OTPs for this email and purpose
        $this->invalidateOldOTPs($email, 'registration');

        $otp = $this->generateRandomOTP();
        
        OtpCode::create([
            'user_id' => null,
            'email' => $email,
            'otp' => Hash::make($otp),
            'purpose' => 'registration',
            'expired_at' => now()->addMinutes(5),
            'verified_at' => null,
        ]);

        return $otp;
    }

    public function invalidateOldOTPs(string $email, string $purpose): void
    {
        OtpCode::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->where('expired_at', '>', now())
            ->update(['expired_at' => now()]);
    }

    public function verifyRegistrationOTP(string $email, string $otp): bool
    {
        return $this->verifyOTP($email, $otp, 'registration');
    }

    public function generateResetPasswordOTP(int $userId, string $email): string
    {
        $this->invalidateOldOTPs($email, 'reset_password');

        $otp = $this->generateRandomOTP();
        
        OtpCode::create([
            'user_id' => $userId,
            'email' => $email,
            'otp' => Hash::make($otp),
            'purpose' => 'reset_password',
            'expired_at' => now()->addMinutes(5),
            'verified_at' => null,
        ]);

        return $otp;
    }

    public function verifyResetPasswordOTP(string $email, string $otp): bool
    {
        return $this->verifyOTP($email, $otp, 'reset_password');
    }

    protected function verifyOTP(string $email, string $otp, string $purpose): bool
    {
        $otpRecord = OtpCode::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return false;
        }

        if (Hash::check($otp, $otpRecord->otp)) {
            $otpRecord->update(['verified_at' => now()]);
            return true;
        }

        return false;
    }

    protected function generateRandomOTP(): string
    {
        return sprintf('%06d', mt_rand(0, 999999));
    }
}
