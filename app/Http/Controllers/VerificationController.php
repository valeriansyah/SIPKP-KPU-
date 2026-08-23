<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyReportRequest;
use App\Models\Report;
use App\Services\VerificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VerificationController extends Controller
{
    use AuthorizesRequests;

    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function store(VerifyReportRequest $request, Report $report)
    {
        // 1. Authorize verification object via ReportPolicy
        $this->authorize('verify', $report);

        // 2. Execute verification
        try {
            $updatedReport = $this->verificationService->verifyReport(
                $request->user(),
                $report,
                $request->decision,
                $request->notes
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Laporan berhasil diverifikasi.',
                    'data' => $updatedReport,
                ]);
            }

            return redirect()->route('sub_operator.antrean')->with('success', 'Laporan berhasil diverifikasi.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
