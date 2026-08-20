<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Deceased;

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
                'notes' => 'Test approval'
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
                'notes' => 'Test rejection'
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
                'notes' => 'Test invalid'
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
}
