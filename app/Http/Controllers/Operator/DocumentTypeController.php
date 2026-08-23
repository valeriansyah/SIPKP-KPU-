<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\StoreDocumentTypeRequest;
use App\Http\Requests\Operator\UpdateDocumentTypeRequest;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $documentTypes = DocumentType::withCount('documents')->latest()->paginate(10);
        return view('operator.master-data.document-types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('operator.master-data.document-types.create');
    }

    public function store(StoreDocumentTypeRequest $request)
    {
        DocumentType::create($request->validated());
        return redirect()->route('operator.master-data.document-types.index')->with('success', 'Jenis Dokumen berhasil ditambahkan.');
    }

    public function edit(DocumentType $documentType)
    {
        return view('operator.master-data.document-types.edit', compact('documentType'));
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {
        $documentType->update($request->validated());
        return redirect()->route('operator.master-data.document-types.index')->with('success', 'Jenis Dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documents()->count() > 0) {
            return back()->with('error', 'Jenis Dokumen tidak dapat dihapus karena sudah digunakan dalam pelaporan.');
        }

        $documentType->delete();
        return redirect()->route('operator.master-data.document-types.index')->with('success', 'Jenis Dokumen berhasil dihapus.');
    }
}
