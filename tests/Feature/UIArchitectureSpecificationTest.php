<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\District;

class UIArchitectureSpecificationTest extends TestCase
{
    use RefreshDatabase;

    protected $operatorRole;
    protected $subOperatorRole;
    protected $pelaporRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
    }

    public function test_ui_architecture_specification_document_exists_and_complete()
    {
        $filePath = base_path('docs/Phase-5B-UI-UX-Design-Specification.md');
        $this->assertFileExists($filePath);

        $content = file_get_contents($filePath);

        // Ensure critical sections are documented
        $this->assertStringContainsString('Page Inventory', $content);
        $this->assertStringContainsString('Role-Based Navigation', $content);
        $this->assertStringContainsString('Status Design', $content);
        $this->assertStringContainsString('Document Upload UI', $content);
        $this->assertStringContainsString('Component Architecture', $content);
        $this->assertStringContainsString('Responsive Design', $content);
        $this->assertStringContainsString('UI Security Principles', $content);
    }

    public function test_operator_is_read_only_towards_verification()
    {
        $operator = User::create([
            'full_name' => 'Operator',
            'username' => 'operator',
            'email' => 'operator@test.com',
            'phone_number' => '081234567890',
            'password' => 'password',
            'role_id' => $this->operatorRole->id,
            'is_active' => true,
        ]);

        $pelapor = User::create([
            'full_name' => 'Pelapor',
            'username' => 'pelapor',
            'email' => 'pelapor@test.com',
            'phone_number' => '081234567891',
            'password' => 'password',
            'role_id' => $this->pelaporRole->id,
            'is_active' => true,
        ]);

        $status = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $report = Report::create([
            'user_id' => $pelapor->id,
            'report_status_id' => $status->id,
            'report_number' => 'TEST-001'
        ]);

        $this->actingAs($operator);

        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'disetujui'
        ]);

        // Access should be strictly forbidden at backend layer
        $response->assertStatus(403);
    }

    public function test_sub_operator_is_district_scoped_for_verification()
    {
        $districtA = District::firstOrCreate(['code' => 'A', 'name' => 'A']);
        $districtB = District::firstOrCreate(['code' => 'B', 'name' => 'B']);

        $subOperator = User::create([
            'full_name' => 'Sub Operator',
            'username' => 'subop',
            'email' => 'subop@test.com',
            'phone_number' => '081234567892',
            'password' => 'password',
            'role_id' => $this->subOperatorRole->id,
            'district_id' => $districtA->id,
            'is_active' => true,
        ]);

        $pelapor = User::create([
            'full_name' => 'Pelapor',
            'username' => 'pelapor',
            'email' => 'pelapor@test.com',
            'phone_number' => '081234567893',
            'password' => 'password',
            'role_id' => $this->pelaporRole->id,
            'is_active' => true,
        ]);

        $status = ReportStatus::firstOrCreate(['status_name' => 'Pending']);

        $report = Report::create([
            'user_id' => $pelapor->id,
            'report_status_id' => $status->id,
            'report_number' => 'TEST-002'
        ]);

        // Attach deceased to district B
        $report->deceased()->create([
            'district_id' => $districtB->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum',
            'gender' => 'Laki-laki',
            'birth_place' => 'Jakarta',
            'address' => 'Jl. Merdeka',
            'birth_date' => '2000-01-01',
            'death_date' => '2020-01-01'
        ]);

        $this->actingAs($subOperator);

        // Sub Operator (District A) attempting to verify Report from District B
        $response = $this->postJson("/reports/{$report->id}/verify", [
            'decision' => 'disetujui'
        ]);

        $response->assertStatus(403);
    }

    public function test_pelapor_is_ownership_scoped()
    {
        $pelapor1 = User::create([
            'full_name' => 'Pelapor 1',
            'username' => 'pelapor1',
            'email' => 'pelapor1@test.com',
            'phone_number' => '081234567894',
            'password' => 'password',
            'role_id' => $this->pelaporRole->id,
            'is_active' => true,
        ]);

        $pelapor2 = User::create([
            'full_name' => 'Pelapor 2',
            'username' => 'pelapor2',
            'email' => 'pelapor2@test.com',
            'phone_number' => '081234567895',
            'password' => 'password',
            'role_id' => $this->pelaporRole->id,
            'is_active' => true,
        ]);

        $status = ReportStatus::firstOrCreate(['status_name' => 'Perlu Perbaikan']);

        $report = Report::create([
            'user_id' => $pelapor2->id, // Owned by Pelapor 2
            'report_status_id' => $status->id,
            'report_number' => 'TEST-003'
        ]);

        $this->actingAs($pelapor1);

        // Pelapor 1 trying to update Report owned by Pelapor 2
        $response = $this->putJson("/reports/{$report->id}");

        $response->assertStatus(403);
    }
}
