<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Phase7IEditLaporanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\ReportStatusSeeder::class);
        $this->seed(\Database\Seeders\DistrictSeeder::class);
    }

    private function createPelaporUser()
    {
        $role = Role::where('role_name', 'Pelapor')->first();
        return User::factory()->create(['role_id' => $role->id]);
    }

    private function createReportForUser($user, $statusName)
    {
        $status = ReportStatus::where('status_name', $statusName)->first();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'report_status_id' => $status->id,
        ]);
        
        Deceased::factory()->create([
            'report_id' => $report->id,
            'district_id' => District::first()->id,
        ]);
        
        return $report;
    }

    public function test_pelapor_can_access_edit_page_for_pending_report()
    {
        $pelapor = $this->createPelaporUser();
        $report = $this->createReportForUser($pelapor, 'Pending');

        $response = $this->actingAs($pelapor)->get(route('pelapor.laporan.edit', $report->id));

        $response->assertStatus(200);
        $response->assertViewIs('pelapor.laporan.edit');
        $response->assertViewHas('report');
        $response->assertViewHas('districts');
    }

    public function test_pelapor_can_access_edit_page_for_perlu_perbaikan_report()
    {
        $pelapor = $this->createPelaporUser();
        $report = $this->createReportForUser($pelapor, 'Perlu Perbaikan');

        $response = $this->actingAs($pelapor)->get(route('pelapor.laporan.edit', $report->id));

        $response->assertStatus(200);
    }

    public function test_pelapor_cannot_access_edit_page_for_diproses_report()
    {
        $pelapor = $this->createPelaporUser();
        $report = $this->createReportForUser($pelapor, 'Diproses');

        $response = $this->actingAs($pelapor)->get(route('pelapor.laporan.edit', $report->id));

        $response->assertStatus(403);
    }

    public function test_pelapor_cannot_access_edit_page_for_disetujui_report()
    {
        $pelapor = $this->createPelaporUser();
        $report = $this->createReportForUser($pelapor, 'Disetujui');

        $response = $this->actingAs($pelapor)->get(route('pelapor.laporan.edit', $report->id));

        $response->assertStatus(403);
    }

    public function test_pelapor_can_update_pending_report_data()
    {
        $pelapor = $this->createPelaporUser();
        $report = $this->createReportForUser($pelapor, 'Pending');
        $district = District::first();

        $updateData = [
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Updated Name',
            'gender' => 'Laki-laki',
            'district_id' => $district->id,
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'address' => 'Updated Address',
            'death_date' => '2023-01-01',
        ];

        $response = $this->actingAs($pelapor)->put(route('pelapor.laporan.update', $report->id), $updateData);

        $response->assertRedirect(route('pelapor.laporan.show', $report->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('deceased', [
            'report_id' => $report->id,
            'name' => 'Updated Name',
        ]);
        
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => ReportStatus::where('status_name', 'Pending')->first()->id,
        ]);
    }
}
