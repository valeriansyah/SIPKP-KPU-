<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\District;
use App\Models\DocumentType;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    use AuthorizesRequests;

    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $reports = $this->reportService->getReportsForUser($request->user());

        if ($request->wantsJson()) {
            return response()->json($reports);
        }

        $roleName = Str::slug($request->user()->role->role_name, '_');
        if ($roleName === 'pelapor') {
            return view('pelapor.laporan.index', compact('reports'));
        } elseif ($roleName === 'sub_operator') {
            return view('sub-operator.antrean', compact('reports'));
        } elseif ($roleName === 'operator_provinsi') {
            $stats = [
                'total' => $reports->count(),
                'pending' => $reports->where('reportStatus.status_name', 'Pending')->count(),
                'diproses' => $reports->where('reportStatus.status_name', 'Diproses')->count(),
                'disetujui' => $reports->where('reportStatus.status_name', 'Disetujui')->count(),
                'ditolak' => $reports->where('reportStatus.status_name', 'Ditolak')->count(),
                'perbaikan' => $reports->where('reportStatus.status_name', 'Perlu Perbaikan')->count(),
            ];
            $districts = District::orderBy('name')->withCount('reports')->get();

            return view('operator.monitoring', compact('reports', 'stats', 'districts'));
        }

        abort(403);
    }

    public function create()
    {
        Gate::authorize('create-report');
        $districts = District::orderBy('name')->get();
        $documentTypes = DocumentType::all();

        return view('pelapor.laporan.create', compact('districts', 'documentTypes'));
    }

    public function store(StoreReportRequest $request)
    {
        Gate::authorize('create-report');
        $data = $request->validated();

        $files = [];
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
        }

        $report = $this->reportService->createReport($request->user(), $data, $files);

        if ($request->wantsJson()) {
            return response()->json($report, 201);
        }

        return redirect()->route('pelapor.laporan.index')->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Request $request, Report $report)
    {
        $this->authorize('view', $report);
        $report = $this->reportService->getReportForUser($request->user(), $report);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        $roleName = Str::slug($request->user()->role->role_name, '_');
        if ($roleName === 'pelapor') {
            return view('pelapor.laporan.show', compact('report'));
        } elseif ($roleName === 'sub_operator') {
            return view('sub-operator.laporan.show', compact('report'));
        } elseif ($roleName === 'operator_provinsi') {
            return view('operator.laporan.show', compact('report'));
        }

        abort(403);
    }

    public function edit(Report $report)
    {
        $this->authorize('update', $report);
        $districts = \App\Models\District::orderBy('name')->get();
        $report->load('revisionItems.documentType');
        $revisionItems = $report->revisionItems->where('is_resolved', false);
        $revisionFields = $revisionItems->where('revision_type', 'data')->pluck('field_name')->toArray();
        $revisionDocuments = $revisionItems->where('revision_type', 'document');

        return view('pelapor.laporan.edit', compact('report', 'districts', 'revisionItems', 'revisionFields', 'revisionDocuments'));
    }

    public function update(UpdateReportRequest $request, Report $report, \App\Services\DocumentService $documentService)
    {
        $this->authorize('update', $report);
        
        $validated = $request->validated();
        
        // Handle document replacements
        if ($request->hasFile('documents')) {
            $revisionDocuments = $report->revisionItems()->where('is_resolved', false)->where('revision_type', 'document')->get()->keyBy('document_type_id');
            foreach ($request->file('documents') as $docTypeId => $file) {
                if (isset($revisionDocuments[$docTypeId])) {
                    // Find the existing document
                    $existingDoc = $report->documents()->where('document_type_id', $docTypeId)->first();
                    if ($existingDoc) {
                        $documentService->replaceDocument($request->user(), $existingDoc, $file);
                    } else {
                        // If it doesn't exist for some reason, upload it as new
                        $docType = \App\Models\DocumentType::find($docTypeId);
                        if ($docType) {
                            $documentService->uploadDocument($request->user(), $report, $docType, $file);
                        }
                    }
                }
            }
        }
        
        $updatedReport = $this->reportService->updateReport($request->user(), $report, $validated);

        // Resolve all revision items since pelapor has submitted the update
        $report->revisionItems()->update(['is_resolved' => true]);

        if ($request->wantsJson()) {
            return response()->json($updatedReport);
        }

        return redirect()->route('pelapor.laporan.show', $report->id)->with('success', 'Laporan berhasil diperbaiki.');
    }
}
