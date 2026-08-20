<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\DocumentType;

class ReportService
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function createReport(User $user, array $data, array $files = []): Report
    {
        $uploadedPaths = [];

        try {
            return DB::transaction(function () use ($user, $data, $files, &$uploadedPaths) {
                $status = ReportStatus::where('status_name', 'Pending')->firstOrFail();
                
                $today = now()->format('Ymd');
                // Safe locking count could be needed in extreme concurrency, but for this scope simple count is fine
                $count = Report::whereDate('created_at', now()->toDateString())->count() + 1;
                $reportNumber = 'SIPKP-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                $report = Report::create([
                    'user_id' => $user->id,
                    'report_status_id' => $status->id,
                    'report_number' => $reportNumber,
                ]);

                $report->deceased()->create([
                    'district_id' => $data['district_id'],
                    'nik' => $data['nik'],
                    'family_card_number' => $data['family_card_number'],
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'birth_place' => $data['birth_place'],
                    'birth_date' => $data['birth_date'],
                    'address' => $data['address'],
                    'death_place' => $data['death_place'] ?? null,
                    'death_date' => $data['death_date'],
                ]);

                // Process documents
                if (!empty($files) && is_array($files)) {
                    // Ensure document types are loaded to prevent N+1 and fail fast if invalid ID
                    $documentTypeIds = array_keys($files);
                    $documentTypes = DocumentType::whereIn('id', $documentTypeIds)->get()->keyBy('id');

                    foreach ($files as $typeId => $file) {
                        if ($file && isset($documentTypes[$typeId])) {
                            $document = $this->documentService->uploadDocument($user, $report, $documentTypes[$typeId], $file);
                            $uploadedPaths[] = $document->file_path;
                        }
                    }
                }

                AuditLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Membuat Laporan',
                    'description' => 'User membuat laporan dengan nomor ' . $report->report_number,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                return $report->load('deceased', 'reportStatus', 'documents');
            });
        } catch (\Exception $e) {
            // Cleanup uploaded files if the transaction fails
            foreach ($uploadedPaths as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
            throw $e;
        }
    }

    public function getReportForUser(User $user, Report $report): Report
    {
        // Object-level authorization is enforced at Controller via Policy
        // This method just formats or eager loads what is necessary
        return $report->load('deceased', 'reportStatus');
    }

    public function getReportsForUser(User $user)
    {
        $roleName = Str::slug($user->role->role_name, '_');

        if ($roleName === 'pelapor') {
            return Report::with(['deceased', 'reportStatus'])
                ->where('user_id', $user->id)
                ->get();
        }

        if ($roleName === 'sub_operator') {
            return Report::with(['deceased', 'reportStatus'])
                ->whereHas('deceased', function ($query) use ($user) {
                    $query->where('district_id', $user->district_id);
                })->get();
        }

        if ($roleName === 'operator_provinsi') {
            return Report::with(['deceased', 'reportStatus'])->get();
        }

        return collect([]);
    }

    public function updateReport(User $user, Report $report, array $data): Report
    {
        return DB::transaction(function () use ($user, $report, $data) {
            $report->deceased->update([
                'district_id' => $data['district_id'] ?? $report->deceased->district_id,
                'nik' => $data['nik'] ?? $report->deceased->nik,
                'family_card_number' => $data['family_card_number'] ?? $report->deceased->family_card_number,
                'name' => $data['name'] ?? $report->deceased->name,
                'gender' => $data['gender'] ?? $report->deceased->gender,
                'birth_place' => $data['birth_place'] ?? $report->deceased->birth_place,
                'birth_date' => $data['birth_date'] ?? $report->deceased->birth_date,
                'address' => $data['address'] ?? $report->deceased->address,
                'death_place' => $data['death_place'] ?? $report->deceased->death_place,
                'death_date' => $data['death_date'] ?? $report->deceased->death_date,
            ]);

            $pendingStatus = ReportStatus::where('status_name', 'Pending')->firstOrFail();
            $report->update([
                'report_status_id' => $pendingStatus->id
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Perbaikan Laporan',
                'description' => 'User memperbaiki laporan dengan nomor ' . $report->report_number,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $report->refresh()->load('deceased', 'reportStatus');
        });
    }
}
