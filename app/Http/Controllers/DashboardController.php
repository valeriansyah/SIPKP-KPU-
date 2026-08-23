<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function pelapor(Request $request)
    {
        $user = $request->user();

        $metrics = $this->dashboardService->getPelaporMetrics($user);
        $recentReports = $this->dashboardService->getPelaporRecentReports($user, 5);

        return view('pelapor.dashboard', compact('metrics', 'recentReports'));
    }

    public function subOperator(Request $request)
    {
        $user = $request->user();

        $metrics = $this->dashboardService->getSubOperatorMetrics($user);
        $queue = $this->dashboardService->getSubOperatorQueue($user, 10);

        return view('sub-operator.dashboard', compact('metrics', 'queue'));
    }

    public function operator(Request $request)
    {
        $metrics = $this->dashboardService->getOperatorMetrics();
        $recentReports = $this->dashboardService->getOperatorRecentReports(5);
        $districtStatistics = $this->dashboardService->getDistrictStatistics();

        // Let's also fetch AuditLogs for Aktivitas Sistem Terakhir
        $activities = AuditLog::with('user', 'user.role')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('operator.dashboard', compact('metrics', 'recentReports', 'districtStatistics', 'activities'));
    }
}
