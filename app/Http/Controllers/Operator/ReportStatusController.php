<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\UpdateReportStatusRequest;
use App\Models\ReportStatus;

class ReportStatusController extends Controller
{
    public function index()
    {
        $reportStatuses = ReportStatus::withCount(['reports'])->get();
        return view('operator.master-data.report-statuses.index', compact('reportStatuses'));
    }

    public function edit(ReportStatus $reportStatus)
    {
        return view('operator.master-data.report-statuses.edit', compact('reportStatus'));
    }

    public function update(UpdateReportStatusRequest $request, ReportStatus $reportStatus)
    {
        // Only update description to protect workflow identifier
        $reportStatus->update([
            'description' => $request->validated('description')
        ]);
        
        return redirect()->route('operator.master-data.report-statuses.index')->with('success', 'Deskripsi Status Laporan berhasil diperbarui.');
    }
}
