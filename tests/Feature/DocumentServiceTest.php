<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use App\Models\ReportStatus;
use App\Models\Report;
use App\Models\Deceased;
use App\Models\DocumentType;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;
use App\Services\DocumentService;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $pelaporRole;
    protected $operatorRole;
    protected $subOperatorRole;
    
    protected $pendingStatus;
    protected $perluPerbaikanStatus;
    protected $disetujuiStatus;

    protected $districtPalembang;
    protected $ktpType;
    protected $kkType;

    protected $documentService;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');

        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);

        $this->pendingStatus = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $this->perluPerbaikanStatus = ReportStatus::firstOrCreate(['status_name' => 'Perlu Perbaikan']);
        $this->disetujuiStatus = ReportStatus::firstOrCreate(['status_name' => 'Disetujui']);

        $this->districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        
        $this->ktpType = DocumentType::firstOrCreate(['name' => 'KTP Almarhum', 'is_required' => true]);
        $this->kkType = DocumentType::firstOrCreate(['name' => 'Kartu Keluarga (KK)', 'is_required' => true]);

        $this->documentService = new DocumentService();
    }

    protected function createUser($role)
    {
        return User::create([
            'full_name' => 'Test ' . $role->role_name,
            'username' => 'testuser_' . uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => true
        ]);
    }

    protected function createReport($user, $status)
    {
        $report = Report::create([
            'user_id' => $user->id,
            'report_status_id' => $status->id,
            'report_number' => 'SIPKP-20260807-0001',
        ]);

        Deceased::create([
            'report_id' => $report->id,
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Test',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
        ]);

        return $report;
    }

    // 1. Document berhasil dibuat.
    public function test_document_can_be_uploaded_successfully()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('ktp.jpg');

        $response = $this->withHeaders(['Accept' => 'application/json'])->post("/reports/{$report->id}/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('documents', [
            'report_id' => $report->id,
            'document_type_id' => $this->ktpType->id,
            'file_name' => 'ktp.jpg',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $pelapor->id,
            'activity' => 'Upload Dokumen',
        ]);
    }

    // 5. Report yang tidak ditemukan ditolak.
    public function test_document_upload_rejected_if_report_not_found()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('ktp.jpg');

        $response = $this->post("/reports/999/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        $response->assertStatus(404);
    }

    // 6. DocumentType yang tidak ditemukan ditolak.
    public function test_document_upload_rejected_if_document_type_not_found()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('ktp.jpg');

        $response = $this->post("/reports/{$report->id}/documents", [
            'document_type_id' => 999,
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('document_type_id');
    }

    // 7. User tidak dapat menyimpan document ke report milik user lain
    public function test_cross_user_access_prevented()
    {
        $pelapor1 = $this->createUser($this->pelaporRole);
        $pelapor2 = $this->createUser($this->pelaporRole);
        
        $reportPelapor1 = $this->createReport($pelapor1, $this->pendingStatus);

        $this->actingAs($pelapor2);

        $file = UploadedFile::fake()->image('ktp.jpg');

        // Pelapor2 mencoba upload ke laporan Pelapor1
        $response = $this->post("/reports/{$reportPelapor1->id}/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        $response->assertStatus(403);
    }

    // Status Validation
    public function test_cannot_upload_document_if_report_status_not_allowed()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        
        // Report status = Disetujui
        $report = $this->createReport($pelapor, $this->disetujuiStatus);

        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('ktp.jpg');

        $response = $this->withHeaders(['Accept' => 'application/json'])->post("/reports/{$report->id}/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        // Policy denies it
        $response->assertStatus(403);
    }

    // Replace / Update document
    public function test_document_can_be_replaced()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->perluPerbaikanStatus);

        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('old_ktp.jpg');
        $oldDocResponse = $this->post("/reports/{$report->id}/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        $oldDocId = $oldDocResponse->json('id');

        $newFile = UploadedFile::fake()->image('new_ktp.jpg');
        $response = $this->post("/documents/{$oldDocId}", [
            'file' => $newFile,
        ]);

        $response->assertStatus(200);

        // Verify old document is soft deleted
        $this->assertSoftDeleted('documents', [
            'id' => $oldDocId,
        ]);

        // Verify new document exists
        $this->assertDatabaseHas('documents', [
            'report_id' => $report->id,
            'file_name' => 'new_ktp.jpg',
        ]);
    }

    // Delete document
    public function test_document_can_be_deleted()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->perluPerbaikanStatus);

        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('ktp.jpg');
        $oldDocResponse = $this->post("/reports/{$report->id}/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        $oldDocId = $oldDocResponse->json('id');

        $response = $this->delete("/documents/{$oldDocId}");
        $response->assertStatus(200);

        $this->assertSoftDeleted('documents', [
            'id' => $oldDocId,
        ]);
    }

    // Privilege escalation prevention
    public function test_operator_cannot_manage_documents()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $operator = $this->createUser($this->operatorRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($operator);

        $file = UploadedFile::fake()->image('ktp.jpg');

        $response = $this->withHeaders(['Accept' => 'application/json'])->post("/reports/{$report->id}/documents", [
            'document_type_id' => $this->ktpType->id,
            'file' => $file,
        ]);

        $response->assertStatus(403);
    }
}
