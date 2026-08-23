<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Str;

class ReportPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
    {
        $roleName = Str::slug($user->role->role_name, '_');

        if ($roleName === 'operator_provinsi') {
            return true; // Operator can see all reports
        }

        if ($roleName === 'sub_operator') {
            return $user->district_id === $report->deceased?->district_id;
        }

        if ($roleName === 'pelapor') {
            return $user->id === $report->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $report): bool
    {
        $roleName = Str::slug($user->role->role_name, '_');

        if ($roleName === 'pelapor') {
            if ($user->id !== $report->user_id) {
                return false;
            }

            $statusStr = Str::slug($report->reportStatus->status_name, '_');

            return $statusStr === 'perlu_perbaikan';
        }

        return false; // Operator and Sub Operator DENIED
    }

    /**
     * Determine whether the user can verify the model.
     */
    public function verify(User $user, Report $report): bool
    {
        $roleName = Str::slug($user->role->role_name, '_');

        if ($roleName === 'sub_operator') {
            return $user->district_id === $report->deceased?->district_id;
        }

        return false; // Operator and Pelapor DENIED
    }

    /**
     * Determine whether the user can upload or manage documents for the model.
     */
    public function manageDocument(User $user, Report $report): bool
    {
        $roleName = Str::slug($user->role->role_name, '_');

        if ($roleName === 'pelapor') {
            if ($user->id !== $report->user_id) {
                return false;
            }

            $statusStr = Str::slug($report->reportStatus->status_name, '_');

            return in_array($statusStr, ['pending', 'perlu_perbaikan']);
        }

        return false; // Operator and Sub Operator DENIED
    }
}
