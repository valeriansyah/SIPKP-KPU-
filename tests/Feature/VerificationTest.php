<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\ReportVerification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_sub_operator_can_approve_report()
    {
        $role = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $district = District::firstOrCreate(['name' => 'Palembang'], ['code' => '16.71']);

        $subOperator = User::factory()->create([
            'role_id' => $role->id,
            'district_id' => $district->id,
        ]);

        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $statusDisetujui = ReportStatus::firstOrCreate(['status_name' => 'Disetujui']);

        $report = Report::factory()->create([
            'user_id' => User::factory()->create()->id,
            'report_status_id' => $statusPending->id,
        ]);

        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => $district->id,
        ]);

        $response = $this->actingAs($subOperator)
            ->post(route('sub_operator.laporan.verifikasi', $report->id), [
                'decision' => 'disetujui',
                'notes' => 'Test approval',
            ]);

        $response->assertRedirect(route('sub_operator.antrean'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => $statusDisetujui->id,
        ]);
    }

    public function test_sub_operator_can_reject_report()
    {
        $role = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $district = District::firstOrCreate(['name' => 'Palembang'], ['code' => '16.71']);

        $subOperator = User::factory()->create([
            'role_id' => $role->id,
            'district_id' => $district->id,
        ]);

        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $statusDitolak = ReportStatus::firstOrCreate(['status_name' => 'Ditolak']);

        $report = Report::factory()->create([
            'user_id' => User::factory()->create()->id,
            'report_status_id' => $statusPending->id,
        ]);

        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => $district->id,
        ]);

        $response = $this->actingAs($subOperator)
            ->post(route('sub_operator.laporan.verifikasi', $report->id), [
                'decision' => 'ditolak',
                'notes' => 'Test rejection',
            ]);

        $response->assertRedirect(route('sub_operator.antrean'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => $statusDitolak->id,
        ]);
    }

    public function test_sub_operator_gets_validation_error_for_invalid_decision()
    {
        $role = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $district = District::firstOrCreate(['name' => 'Palembang'], ['code' => '16.71']);

        $subOperator = User::factory()->create([
            'role_id' => $role->id,
            'district_id' => $district->id,
        ]);

        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $report = Report::factory()->create([
            'user_id' => User::factory()->create()->id,
            'report_status_id' => $statusPending->id,
        ]);

        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => $district->id,
        ]);

        $response = $this->actingAs($subOperator)
            ->post(route('sub_operator.laporan.verifikasi', $report->id), [
                'decision' => 'approve', // Invalid decision (this caused the bug!)
                'notes' => 'Test invalid',
            ]);

        // Assert validation error and redirect
        $response->assertStatus(302);
        $response->assertSessionHasErrors('decision');

        // Assert DB status is unchanged
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => $statusPending->id,
        ]);
    }

    public function test_sub_operator_can_see_report_details_and_documents()
    {
        $role = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $district = District::firstOrCreate(['name' => 'Palembang'], ['code' => '16.71']);

        $subOperator = User::factory()->create([
            'role_id' => $role->id,
            'district_id' => $district->id,
        ]);

        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $report = Report::factory()->create([
            'user_id' => User::factory()->create()->id,
            'report_status_id' => $statusPending->id,
            'report_number' => 'TEST-1234',
        ]);

        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => $district->id,
            'name' => 'Budi Santoso',
        ]);

        $documentType = DocumentType::firstOrCreate(['name' => 'KTP Almarhum']);

        Document::create([
            'report_id' => $report->id,
            'document_type_id' => $documentType->id,
            'file_name' => 'ktp.jpg',
            'file_path' => 'dummy/ktp.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($subOperator)
            ->get(route('sub_operator.laporan.show', $report->id));

        $response->assertStatus(200);
        $response->assertSee('TEST-1234');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Lampiran Dokumen');
        $response->assertSee('KTP Almarhum');
        $response->assertSee('File demo tidak tersedia'); // dummy
    }

    public function test_sub_operator_sees_empty_state_for_documents()
    {
        $role = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $district = District::firstOrCreate(['name' => 'Palembang'], ['code' => '16.71']);

        $subOperator = User::factory()->create([
            'role_id' => $role->id,
            'district_id' => $district->id,
        ]);

        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $report = Report::factory()->create([
            'user_id' => User::factory()->create()->id,
            'report_status_id' => $statusPending->id,
        ]);

        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => $district->id,
        ]);

        $response = $this->actingAs($subOperator)
            ->get(route('sub_operator.laporan.show', $report->id));

        $response->assertStatus(200);
        $response->assertSee('Belum ada lampiran dokumen pada laporan ini.');
    }

    public function test_sub_operator_can_see_report_details_with_verification_history()
    {
        $role = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $district = District::firstOrCreate(['name' => 'Palembang'], ['code' => '16.71']);

        $subOperator = User::factory()->create([
            'full_name' => 'Budi Verifikator',
            'role_id' => $role->id,
            'district_id' => $district->id,
        ]);

        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $report = Report::factory()->create([
            'user_id' => User::factory()->create()->id,
            'report_status_id' => $statusPending->id,
        ]);

        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => $district->id,
        ]);

        ReportVerification::create([
            'report_id' => $report->id,
            'user_id' => $subOperator->id,
            'report_status_id' => $statusPending->id,
            'notes' => 'Tolong lengkapi dokumen ini.',
        ]);

        $response = $this->actingAs($subOperator)
            ->get(route('sub_operator.laporan.show', $report->id));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Verifikasi');
        $response->assertSee('Tolong lengkapi dokumen ini.');
        $response->assertSee('Budi Verifikator');
    }
}
