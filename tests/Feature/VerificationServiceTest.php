<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\VerificationService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $pelaporRole;

    protected $operatorRole;

    protected $subOperatorRole;

    protected $pendingStatus;

    protected $diprosesStatus;

    protected $perluPerbaikanStatus;

    protected $disetujuiStatus;

    protected $ditolakStatus;

    protected $districtPalembang;

    protected $districtLahat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);

        $this->pendingStatus = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $this->diprosesStatus = ReportStatus::firstOrCreate(['status_name' => 'Diproses']);
        $this->perluPerbaikanStatus = ReportStatus::firstOrCreate(['status_name' => 'Perlu Perbaikan']);
        $this->disetujuiStatus = ReportStatus::firstOrCreate(['status_name' => 'Disetujui']);
        $this->ditolakStatus = ReportStatus::firstOrCreate(['status_name' => 'Ditolak']);

        $this->districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $this->districtLahat = District::firstOrCreate(['name' => 'Lahat', 'code' => '1604']);
    }

    protected function createUser($role, $district = null)
    {
        return User::create([
            'full_name' => 'Test '.$role->role_name,
            'username' => 'testuser_'.uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_'.uniqid().'@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => $district ? $district->id : null,
            'is_active' => true,
        ]);
    }

    protected function createReport($user, $district, $status)
    {
        $report = Report::create([
            'user_id' => $user->id,
            'report_status_id' => $status->id,
            'report_number' => 'SIPKP-20260807-'.rand(1000, 9999),
        ]);

        Deceased::create([
            'report_id' => $report->id,
            'district_id' => $district->id,
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

    // 1. Sub Operator dapat verify report wilayahnya
    public function test_sub_operator_can_verify_report_in_their_district()
    {
        $subOperator = $this->createUser($this->subOperatorRole, $this->districtPalembang);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->pendingStatus);

        $this->actingAs($subOperator);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'diproses',
            'notes' => 'Sedang diperiksa',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => $this->diprosesStatus->id,
        ]);

        $this->assertDatabaseHas('report_verifications', [
            'report_id' => $report->id,
            'user_id' => $subOperator->id,
            'report_status_id' => $this->diprosesStatus->id,
            'notes' => 'Sedang diperiksa',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $subOperator->id,
            'activity' => 'Report Verification',
        ]);
    }

    // 2. Operator tidak dapat verify
    public function test_operator_cannot_verify_report()
    {
        $operator = $this->createUser($this->operatorRole);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->pendingStatus);

        $this->actingAs($operator);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'disetujui',
            'notes' => 'OK',
        ]);

        $response->assertStatus(403);
    }

    // 3. Pelapor tidak dapat verify
    public function test_pelapor_cannot_verify_report()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->pendingStatus);

        $this->actingAs($pelapor);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'disetujui',
            'notes' => 'OK',
        ]);

        $response->assertStatus(403);
    }

    // 4. Sub Operator tidak dapat verify report wilayah lain (IDOR Prevention)
    public function test_sub_operator_cannot_verify_report_in_other_district()
    {
        $subOperator = $this->createUser($this->subOperatorRole, $this->districtLahat);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->pendingStatus);

        $this->actingAs($subOperator);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'disetujui',
            'notes' => 'OK',
        ]);

        $response->assertStatus(403);
    }

    // 5. User tidak authenticated tidak dapat verify
    public function test_unauthenticated_user_cannot_verify()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->pendingStatus);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'disetujui',
            'notes' => 'OK',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    // 6. Report tidak ditemukan
    public function test_verify_returns_404_if_report_not_found()
    {
        $subOperator = $this->createUser($this->subOperatorRole, $this->districtPalembang);
        $this->actingAs($subOperator);

        $response = $this->postJson('/reports/9999/verify', [
            'decision' => 'disetujui',
        ]);

        $response->assertStatus(404);
    }

    // 7. Invalid decision ditolak
    public function test_invalid_decision_is_rejected_by_validation()
    {
        $subOperator = $this->createUser($this->subOperatorRole, $this->districtPalembang);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->pendingStatus);

        $this->actingAs($subOperator);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'dihapus', // Not in whitelist
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('decision');
    }

    // 8. Invalid status transition ditolak (cannot revert back to Pending)
    public function test_cannot_revert_to_pending()
    {
        $subOperator = $this->createUser($this->subOperatorRole, $this->districtPalembang);
        $pelapor = $this->createUser($this->pelaporRole);
        $report = $this->createReport($pelapor, $this->districtPalembang, $this->diprosesStatus);

        $this->actingAs($subOperator);

        // Bypass validation just to test the Service logic
        $service = app(VerificationService::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Sub Operator tidak dapat mengembalikan status menjadi Pending.');

        // Decision 'pending' is not accepted by VerifyReportRequest, but we test the service layer directly
        $service->verifyReport($subOperator, $report, 'pending', 'Batal diproses');
    }

    // 14. Verification kedua pada report yang sudah final ditolak
    public function test_cannot_verify_report_that_is_already_rejected_or_approved()
    {
        $subOperator = $this->createUser($this->subOperatorRole, $this->districtPalembang);
        $pelapor = $this->createUser($this->pelaporRole);

        $reportDitolak = $this->createReport($pelapor, $this->districtPalembang, $this->ditolakStatus);

        $this->actingAs($subOperator);

        $response1 = $this->postJson("/reports/{$reportDitolak->id}/verify", [
            'decision' => 'perlu_perbaikan',
        ]);

        $response1->assertStatus(422); // Handled by catch block in controller
        $this->assertStringContainsString('ditolak secara permanen', $response1->getContent());

        $reportDisetujui = $this->createReport($pelapor, $this->districtPalembang, $this->disetujuiStatus);

        $response2 = $this->postJson("/reports/{$reportDisetujui->id}/verify", [
            'decision' => 'diproses',
        ]);

        $response2->assertStatus(422);
        $this->assertStringContainsString('sudah disetujui', $response2->getContent());
    }
}
