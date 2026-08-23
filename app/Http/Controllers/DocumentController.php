<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplaceDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Report;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function store(StoreDocumentRequest $request, Report $report)
    {
        // Object-level authorization via ReportPolicy (manageDocument)
        $this->authorize('manageDocument', $report);

        $documentType = DocumentType::findOrFail($request->document_type_id);

        try {
            $document = $this->documentService->uploadDocument(
                $request->user(),
                $report,
                $documentType,
                $request->file('file')
            );

            return response()->json($document, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(ReplaceDocumentRequest $request, Document $document)
    {
        // Object-level authorization via ReportPolicy (manageDocument)
        $this->authorize('manageDocument', $document->report);

        try {
            $newDocument = $this->documentService->replaceDocument(
                $request->user(),
                $document,
                $request->file('file')
            );

            return response()->json($newDocument);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request, Document $document)
    {
        // Object-level authorization via ReportPolicy (manageDocument)
        $this->authorize('manageDocument', $document->report);

        try {
            $this->documentService->deleteDocument($request->user(), $document);

            return response()->json(['message' => 'Document deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request, Document $document)
    {
        $this->authorize('view', $document->report);

        $path = $document->file_path;
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path);
        } elseif (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        return redirect()->back()->with('error', 'File dokumen fisik tidak ditemukan di server.');
    }

    public function download(Request $request, Document $document)
    {
        $this->authorize('view', $document->report);

        $path = $document->file_path;
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path, $document->file_name);
        } elseif (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, $document->file_name);
        }

        return redirect()->back()->with('error', 'File dokumen fisik tidak ditemukan di server.');
    }
}
