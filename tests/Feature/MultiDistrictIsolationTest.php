<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use App\Models\Report;
use App\Models\Deceased;
use App\Models\ReportStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiDistrictIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutVite();

        // Create Roles
        Role::create(['role_name' => 'Operator Provinsi']);
        Role::create(['role_name' => 'Sub Operator']);
        Role::create(['role_name' => 'Pelapor']);

        // Create Status
        ReportStatus::create(['status_name' => 'Pending']);

        // Create Districts
        District::create(['name' => 'Palembang', 'code' => '1671']);
        District::create(['name' => 'Lahat', 'code' => '1604']);
    }

    public function test_sub_operator_can_only_access_their_own_district_reports()
    {
        $subOpRole = Role::where('role_name', 'Sub Operator')->first();
        $palembang = District::where('name', 'Palembang')->first();
        $lahat = District::where('name', 'Lahat')->first();
        $pending = ReportStatus::first();

        // Create Sub Operators
        $subOpPalembang = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $palembang->id,
            'is_active' => true,
        ]);

        $subOpLahat = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $lahat->id,
            'is_active' => true,
        ]);

        $pelapor = User::factory()->create([
            'role_id' => Role::where('role_name', 'Pelapor')->first()->id,
            'is_active' => true,
        ]);

        // Create Reports
        $reportPalembang = Report::factory()->create([
            'user_id' => $pelapor->id,
            'report_status_id' => $pending->id,
        ]);
        Deceased::factory()->create([
            'report_id' => $reportPalembang->id,
            'district_id' => $palembang->id,
        ]);

        $reportLahat = Report::factory()->create([
            'user_id' => $pelapor->id,
            'report_status_id' => $pending->id,
        ]);
        Deceased::factory()->create([
            'report_id' => $reportLahat->id,
            'district_id' => $lahat->id,
        ]);

        // Test Palembang Sub Op
        $this->actingAs($subOpPalembang);
        
        // Can view palembang report
        $response = $this->get('/sub-operator/laporan/' . $reportPalembang->id);
        $response->assertStatus(200);

        // Cannot view lahat report
        $response = $this->get('/sub-operator/laporan/' . $reportLahat->id);
        $response->assertStatus(403);
        
        // Cannot access pelapor route
        $response = $this->get('/pelapor/laporan/create');
        $response->assertStatus(403);

        // Test Lahat Sub Op
        $this->actingAs($subOpLahat);
        
        // Can view lahat report
        $response = $this->get('/sub-operator/laporan/' . $reportLahat->id);
        $response->assertStatus(200);

        // Cannot view palembang report
        $response = $this->get('/sub-operator/laporan/' . $reportPalembang->id);
        $response->assertStatus(403);
    }
}
