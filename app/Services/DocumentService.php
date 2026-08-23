<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Report;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Store a new document for a report.
     */
    public function uploadDocument(User $user, Report $report, DocumentType $documentType, UploadedFile $file): Document
    {
        return DB::transaction(function () use ($user, $report, $documentType, $file) {
            // Defensive validation: ensure report status allows document upload
            // Usually handled by policy, but we add it here for defense in depth
            $statusStr = Str::slug($report->reportStatus->status_name, '_');
            if (! in_array($statusStr, ['pending', 'perlu_perbaikan'])) {
                throw new Exception('Laporan dengan status ini tidak dapat menerima dokumen.');
            }

            // Store physical file
            // Format: documents/{report_number}/{timestamp}_{filename}
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('documents/'.$report->report_number, $filename, 'local');

            // Store metadata
            $document = Document::create([
                'report_id' => $report->id,
                'document_type_id' => $documentType->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            // Create audit log
            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Upload Dokumen',
                'description' => 'User mengunggah dokumen '.$documentType->name.' untuk laporan '.$report->report_number,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $document->load('documentType');
        });
    }

    /**
     * Replace an existing document.
     */
    public function replaceDocument(User $user, Document $document, UploadedFile $newFile): Document
    {
        return DB::transaction(function () use ($user, $document, $newFile) {
            $report = $document->report;
            $documentType = $document->documentType;

            $statusStr = Str::slug($report->reportStatus->status_name, '_');
            if (! in_array($statusStr, ['pending', 'perlu_perbaikan'])) {
                throw new Exception('Laporan dengan status ini tidak dapat mengganti dokumen.');
            }

            // Soft delete the old document
            $document->delete();

            // Store new physical file
            $filename = time().'_'.$newFile->getClientOriginalName();
            $path = $newFile->storeAs('documents/'.$report->report_number, $filename, 'local');

            // Store new metadata
            $newDocument = Document::create([
                'report_id' => $report->id,
                'document_type_id' => $documentType->id,
                'file_name' => $newFile->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $newFile->getClientMimeType(),
                'file_size' => $newFile->getSize(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Ganti Dokumen',
                'description' => 'User mengganti dokumen '.$documentType->name.' pada laporan '.$report->report_number,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $newDocument->load('documentType');
        });
    }

    /**
     * Soft delete a document.
     */
    public function deleteDocument(User $user, Document $document): bool
    {
        return DB::transaction(function () use ($user, $document) {
            $report = $document->report;
            $documentType = $document->documentType;

            $statusStr = Str::slug($report->reportStatus->status_name, '_');
            if (! in_array($statusStr, ['pending', 'perlu_perbaikan'])) {
                throw new Exception('Laporan dengan status ini tidak dapat menghapus dokumen.');
            }

            $document->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'Hapus Dokumen',
                'description' => 'User menghapus dokumen '.$documentType->name.' dari laporan '.$report->report_number,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return true;
        });
    }
}
