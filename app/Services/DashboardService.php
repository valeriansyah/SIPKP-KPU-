<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get metrics for a Pelapor.
     */
    public function getPelaporMetrics(User $user): array
    {
        $query = Report::where('user_id', $user->id);

        return $this->getMetricsForQuery($query);
    }

    /**
     * Get recent reports for a Pelapor.
     */
    public function getPelaporRecentReports(User $user, int $limit = 5)
    {
        return Report::with(['deceased', 'reportStatus'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get metrics for a Sub Operator based on their district.
     */
    public function getSubOperatorMetrics(User $user): array
    {
        $query = Report::whereHas('deceased', function ($q) use ($user) {
            $q->where('district_id', $user->district_id);
        });

        return $this->getMetricsForQuery($query);
    }

    /**
     * Get paginated queue for a Sub Operator based on their district.
     */
    public function getSubOperatorQueue(User $user, int $perPage = 10)
    {
        return Report::with(['deceased', 'reportStatus', 'user'])
            ->whereHas('deceased', function ($q) use ($user) {
                $q->where('district_id', $user->district_id);
            })
            ->orderByRaw("
                CASE 
                    WHEN report_status_id = (SELECT id FROM report_statuses WHERE status_name = 'Pending') THEN 1
                    WHEN report_status_id = (SELECT id FROM report_statuses WHERE status_name = 'Diproses') THEN 2
                    WHEN report_status_id = (SELECT id FROM report_statuses WHERE status_name = 'Perlu Perbaikan') THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('created_at', 'asc') // Oldest first for queue
            ->paginate($perPage);
    }

    /**
     * Get global metrics for Operator.
     */
    public function getOperatorMetrics(): array
    {
        $query = Report::query();

        return $this->getMetricsForQuery($query);
    }

    /**
     * Get global district statistics for Operator chart.
     */
    public function getDistrictStatistics()
    {
        // District distribution
        return DB::table('reports')
            ->join('deceased', 'reports.id', '=', 'deceased.report_id')
            ->join('districts', 'deceased.district_id', '=', 'districts.id')
            ->select('districts.name as district', DB::raw('COUNT(reports.id) as total'))
            ->groupBy('districts.id', 'districts.name')
            ->orderBy('districts.name')
            ->get();
    }

    /**
     * Get recent global reports for Operator.
     */
    public function getOperatorRecentReports(int $limit = 5)
    {
        return Report::with(['deceased', 'reportStatus', 'deceased.district'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Helper to compute metrics from a base query.
     */
    private function getMetricsForQuery($query): array
    {
        // Clone query to safely modify it
        $statusCounts = (clone $query)->select('report_status_id', DB::raw('count(*) as total'))
            ->groupBy('report_status_id')
            ->pluck('total', 'report_status_id');

        $statuses = ReportStatus::all()->keyBy('id');

        $metrics = [
            'total' => $statusCounts->sum(),
            'pending' => 0,
            'diproses' => 0,
            'perlu_perbaikan' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
        ];

        foreach ($statusCounts as $statusId => $count) {
            $statusName = $statuses[$statusId]->status_name ?? '';
            switch ($statusName) {
                case 'Pending':
                    $metrics['pending'] = $count;
                    break;
                case 'Diproses':
                    $metrics['diproses'] = $count;
                    break;
                case 'Perlu Perbaikan':
                    $metrics['perlu_perbaikan'] = $count;
                    break;
                case 'Disetujui':
                    $metrics['disetujui'] = $count;
                    break;
                case 'Ditolak':
                    $metrics['ditolak'] = $count;
                    break;
            }
        }

        return $metrics;
    }
}
