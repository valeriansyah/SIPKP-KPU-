<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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
        Storage::fake('local');

        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);

        $this->pendingStatus = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $this->perluPerbaikanStatus = ReportStatus::firstOrCreate(['status_name' => 'Perlu Perbaikan']);
        $this->disetujuiStatus = ReportStatus::firstOrCreate(['status_name' => 'Disetujui']);

        $this->districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);

        $this->ktpType = DocumentType::firstOrCreate(['name' => 'KTP Almarhum', 'is_required' => true]);
        $this->kkType = DocumentType::firstOrCreate(['name' => 'Kartu Keluarga (KK)', 'is_required' => true]);

        $this->documentService = new DocumentService;
    }

    protected function createUser($role)
    {
        return User::create([
            'full_name' => 'Test '.$role->role_name,
            'username' => 'testuser_'.uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_'.uniqid().'@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'is_active' => true,
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
        $this->actingAs($pelapor);

        $data = [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Upload',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
            'documents' => [
                $this->ktpType->id => \Illuminate\Http\UploadedFile::fake()->image('ktp.jpg'),
                2 => \Illuminate\Http\UploadedFile::fake()->image('doc2.pdf'),
                3 => \Illuminate\Http\UploadedFile::fake()->image('doc3.pdf'),
                6 => \Illuminate\Http\UploadedFile::fake()->image('doc6.pdf'),
            ],
        ];

        $response = $this->postJson('/pelapor/laporan', $data);
        $response->assertStatus(201);

        $this->assertDatabaseHas('documents', [
            'document_type_id' => $this->ktpType->id,
            'file_name' => 'ktp.jpg',
        ]);
    }


    

    // Replace / Update document
    public function test_document_can_be_replaced()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->perluPerbaikanStatus);

        $this->actingAs($pelapor);

        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $oldDocId = $doc->id;

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
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $oldDocId = $doc->id;

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
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);

        $response = $this->delete("/documents/{$doc->id}");

        $response->assertStatus(403);
    }

    // Security Tests for Document Preview and Download
    public function test_missing_document_returns_graceful_redirect()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        // Create a document record pointing to a missing file
        $document = Document::create([
            'report_id' => $report->id,
            'document_type_id' => $this->ktpType->id,
            'file_name' => 'missing.pdf',
            'file_path' => 'dummy/sipkp/missing.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 12345,
        ]);

        $this->actingAs($pelapor);
        $response = $this->get("/documents/{$document->id}/preview");

        // Should not be 500 or 404, but a redirect back with error
        $response->assertStatus(302);
        $response->assertSessionHas('error', 'File dokumen fisik tidak ditemukan di server.');
    }

    public function test_pelapor_can_preview_own_document()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);
        $file = UploadedFile::fake()->image('ktp.jpg');
        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $docId = $doc->id;
        $response = $this->get("/documents/{$docId}/preview");

        $response->assertStatus(200);
    }

    public function test_pelapor_cannot_preview_other_document()
    {
        $pelapor1 = $this->createUser($this->pelaporRole);
        $pelapor2 = $this->createUser($this->pelaporRole);
        $report1 = $this->createReport($pelapor1, $this->pendingStatus);

        $this->actingAs($pelapor1);
        $file = UploadedFile::fake()->image('ktp.jpg');
        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor1, $report1, $this->ktpType, $file);
        $docId = $doc->id;

        $this->actingAs($pelapor2);
        $response = $this->get("/documents/{$docId}/preview");

        $response->assertStatus(403);
    }

    public function test_sub_operator_can_preview_district_document()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);
        $file = UploadedFile::fake()->image('ktp.jpg');
        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $docId = $doc->id;

        $subOperator = $this->createUser($this->subOperatorRole);
        $subOperator->district_id = $report->deceased->district_id;
        $subOperator->save();

        $this->actingAs($subOperator);
        $response = $this->get("/documents/{$docId}/preview");

        $response->assertStatus(200);
    }

    public function test_sub_operator_cannot_preview_other_district_document()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);
        $file = UploadedFile::fake()->image('ktp.jpg');
        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $docId = $doc->id;

        $subOperator = $this->createUser($this->subOperatorRole);
        
        // Create a different district
        $otherDistrict = District::firstOrCreate(['name' => 'Prabumulih', 'code' => '1672']);
        $subOperator->district_id = $otherDistrict->id;
        $subOperator->save();

        $this->actingAs($subOperator);
        $response = $this->get("/documents/{$docId}/preview");

        $response->assertStatus(403);
    }

    public function test_operator_provinsi_can_preview_any_document()
    {
        $operatorProvinsiRole = Role::firstOrCreate(['role_name' => 'Operator Provinsi']);

        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);
        $file = UploadedFile::fake()->image('ktp.jpg');
        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $docId = $doc->id;

        $operatorProvinsi = $this->createUser($operatorProvinsiRole);

        $this->actingAs($operatorProvinsi);
        $response = $this->get("/documents/{$docId}/preview");

        $response->assertStatus(200);
    }

    public function test_unauthenticated_cannot_preview_document()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->pendingStatus);

        $this->actingAs($pelapor);
        $file = UploadedFile::fake()->image('ktp.jpg');
        $file = UploadedFile::fake()->image('ktp.jpg');
        $doc = app(\App\Services\DocumentService::class)->uploadDocument($pelapor, $report, $this->ktpType, $file);
        $docId = $doc->id;

        // Log out
        auth()->logout();

        $response = $this->get("/documents/{$docId}/preview");

        $response->assertRedirect(route('login'));
    }
}
