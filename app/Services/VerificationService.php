<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\ReportVerification;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerificationService
{
    /**
     * Verify a report and update its status.
     *
     * @param  User  $user  The authenticated Sub Operator
     * @param  Report  $report  The report to verify
     * @param  string  $decision  The slug of the target status ('diproses', 'perlu-perbaikan', 'disetujui', 'ditolak')
     * @param  string|null  $notes  Verification notes
     */
    public function verifyReport(User $user, Report $report, string $decision, ?string $notes): Report
    {
        return DB::transaction(function () use ($user, $report, $decision, $notes) {
            $currentStatusSlug = Str::slug($report->reportStatus->status_name, '_');

            // 1. Validasi Status Transisi
            if ($currentStatusSlug === 'ditolak') {
                throw new Exception('Laporan yang sudah ditolak secara permanen tidak dapat diverifikasi ulang.');
            }

            if ($currentStatusSlug === 'disetujui') {
                throw new Exception('Laporan yang sudah disetujui tidak dapat diverifikasi ulang.');
            }

            $statuses = ReportStatus::all();
            $newStatus = $statuses->first(function ($status) use ($decision) {
                return Str::slug($status->status_name, '_') === Str::slug($decision, '_');
            });

            if (! $newStatus) {
                throw new Exception('Keputusan verifikasi tidak valid.');
            }

            if (Str::slug($newStatus->status_name, '_') === 'pending') {
                throw new Exception('Sub Operator tidak dapat mengembalikan status menjadi Pending.');
            }

            // 2. Update status Report
            $report->update([
                'report_status_id' => $newStatus->id,
            ]);

            // 3. Simpan riwayat verifikasi
            ReportVerification::create([
                'report_id' => $report->id,
                'user_id' => $user->id,
                'report_status_id' => $newStatus->id,
                'notes' => $notes,
            ]);

            // 4. Catat Audit Log
            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Report Verification',
                'description' => 'User melakukan verifikasi laporan '.$report->report_number.' dengan keputusan '.$newStatus->status_name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 5. Kirim Notifikasi Email
            $newStatusSlug = Str::slug($newStatus->status_name, '_');
            if ($currentStatusSlug !== $newStatusSlug) {
                try {
                    $pelapor = $report->user;
                    if ($pelapor && !empty($pelapor->email)) {
                        if ($newStatusSlug === 'perlu_perbaikan') {
                            $pelapor->notify(new \App\Notifications\ReportNeedsRevisionNotification($report, $notes));
                        } elseif ($newStatusSlug === 'disetujui' || $newStatusSlug === 'selesai') {
                            $pelapor->notify(new \App\Notifications\ReportCompletedNotification($report));
                        }
                    }
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send report status notification: ' . $e->getMessage(), [
                        'report_id' => $report->id,
                        'user_id' => $report->user_id,
                    ]);
                }
            }

            return $report->refresh()->load('reportStatus', 'reportVerifications');
        });
    }
}
