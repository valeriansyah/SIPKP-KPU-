<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Report;
use App\Models\DocumentType;
use App\Models\ReportRevisionItem;
use App\Models\ReportStatus;
use App\Notifications\ReportNeedsRevisionNotification;

class ReportRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class); // Run basic seeders
    }

    public function test_sub_operator_can_request_data_revision()
    {
        Notification::fake();
        
        $roleSubOperator = \App\Models\Role::where('role_name', 'Sub Operator')->first();
        $rolePelapor = \App\Models\Role::where('role_name', 'Pelapor')->first();
        $subOperator = User::factory()->create(['role_id' => $roleSubOperator->id, 'district_id' => 1]);
        $pelapor = User::factory()->create(['role_id' => $rolePelapor->id]);
        
        $perluPerbaikanStatus = ReportStatus::where('status_name', 'Pending')->first();
        $report = Report::factory()->create(['user_id' => $pelapor->id, 'report_status_id' => $perluPerbaikanStatus->id]);
        $report->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $subOperator->district_id])->toArray());
        
        $response = $this->actingAs($subOperator)->post(route('sub_operator.laporan.verifikasi', $report->id), [
            'decision' => 'perlu_perbaikan',
            'notes' => 'Tolong perbaiki tanggal lahir',
            'revision_fields' => ['birth_date'],
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('report_revision_items', [
            'report_id' => $report->id,
            'revision_type' => 'data',
            'field_name' => 'birth_date',
            'is_resolved' => false,
        ]);
        
        Notification::assertSentTo($pelapor, ReportNeedsRevisionNotification::class);
    }

    public function test_sub_operator_can_request_document_revision()
    {
        $roleSubOperator = \App\Models\Role::where('role_name', 'Sub Operator')->first();
        $rolePelapor = \App\Models\Role::where('role_name', 'Pelapor')->first();
        $subOperator = User::factory()->create(['role_id' => $roleSubOperator->id, 'district_id' => 1]);
        $pelapor = User::factory()->create(['role_id' => $rolePelapor->id]);
        
        $perluPerbaikanStatus = ReportStatus::where('status_name', 'Pending')->first();
        $report = Report::factory()->create(['user_id' => $pelapor->id, 'report_status_id' => $perluPerbaikanStatus->id]);
        $report->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $subOperator->district_id])->toArray());
        
        $docType = DocumentType::first();
        
        $report->documents()->create([
            'document_type_id' => $docType->id,
            'file_name' => 'test.pdf',
            'file_path' => 'documents/test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);
        
        $response = $this->actingAs($subOperator)->post(route('sub_operator.laporan.verifikasi', $report->id), [
            'decision' => 'perlu_perbaikan',
            'notes' => 'KTP buram',
            'revision_documents' => [$docType->id],
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('report_revision_items', [
            'report_id' => $report->id,
            'revision_type' => 'document',
            'document_type_id' => $docType->id,
            'is_resolved' => false,
        ]);
    }

    public function test_perlu_perbaikan_fails_without_selecting_items()
    {
        $roleSubOperator = \App\Models\Role::where('role_name', 'Sub Operator')->first();
        $subOperator = User::factory()->create(['role_id' => $roleSubOperator->id, 'district_id' => 1]);
        $report = Report::factory()->create([
            'report_status_id' => ReportStatus::where('status_name', 'Pending')->first()->id
        ]);
        $report->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $subOperator->district_id])->toArray());
        
        $response = $this->actingAs($subOperator)->post(route('sub_operator.laporan.verifikasi', $report->id), [
            'decision' => 'perlu_perbaikan',
            'notes' => 'Perbaiki',
            // No fields or documents selected
        ]);

        $response->assertSessionHasErrors('decision');
        $this->assertEquals(0, ReportRevisionItem::count());
    }

    public function test_pelapor_can_submit_revision_and_resolves_items()
    {
        Storage::fake('local');
        
        $rolePelapor = \App\Models\Role::where('role_name', 'Pelapor')->first();
        $pelapor = User::factory()->create(['role_id' => $rolePelapor->id]);
        
        $perluPerbaikanStatus = ReportStatus::where('status_name', 'Perlu Perbaikan')->first();
        
        $report = Report::factory()->create([
            'user_id' => $pelapor->id,
            'report_status_id' => $perluPerbaikanStatus->id
        ]);
        $report->deceased()->create(\App\Models\Deceased::factory()->make()->toArray());
        $report->refresh();
        $docType = DocumentType::first();
        
        $report->documents()->create([
            'document_type_id' => $docType->id,
            'file_name' => 'old.pdf',
            'file_path' => 'documents/old.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);
        
        ReportRevisionItem::create([
            'report_id' => $report->id,
            'revision_type' => 'data',
            'field_name' => 'name',
            'label' => 'Nama',
            'is_resolved' => false,
        ]);
        
        ReportRevisionItem::create([
            'report_id' => $report->id,
            'revision_type' => 'document',
            'document_type_id' => $docType->id,
            'label' => 'KTP',
            'is_resolved' => false,
        ]);
        
        $response = $this->actingAs($pelapor)->put(route('pelapor.laporan.update', $report->id), [
            'name' => 'Nama Baru Almarhum',
            'nik' => $report->deceased->nik,
            'family_card_number' => $report->deceased->family_card_number,
            'gender' => $report->deceased->gender,
            'district_id' => $report->deceased->district_id,
            'birth_place' => $report->deceased->birth_place,
            'birth_date' => $report->deceased->birth_date->format('Y-m-d'),
            'death_date' => $report->deceased->death_date->format('Y-m-d'),
            'address' => $report->deceased->address,
            'documents' => [
                $docType->id => UploadedFile::fake()->create('new.pdf', 100, 'application/pdf')
            ]
        ]);

        $response->assertRedirect(route('pelapor.laporan.show', $report->id));
        
        $this->assertDatabaseHas('deceased', [
            'id' => $report->deceased->id,
            'name' => 'Nama Baru Almarhum',
        ]);
        
        $this->assertSoftDeleted('documents', [
            'report_id' => $report->id,
            'file_name' => 'old.pdf'
        ]);
        
        $this->assertDatabaseHas('documents', [
            'report_id' => $report->id,
            'file_name' => 'new.pdf'
        ]);
        
        $this->assertEquals(0, ReportRevisionItem::where('is_resolved', false)->count());
        $this->assertEquals(2, ReportRevisionItem::where('is_resolved', true)->count());
    }
}
