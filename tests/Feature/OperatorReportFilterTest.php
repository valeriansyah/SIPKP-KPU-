<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorReportFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // create basic data
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\ReportStatusSeeder::class);
    }

    public function test_operator_provinsi_sees_all_reports_without_filter()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $districtA = District::factory()->create(['name' => 'District A']);
        $districtB = District::factory()->create(['name' => 'District B']);

        $reportA = Report::factory()->create();
        $reportA->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id])->toArray());

        $reportB = Report::factory()->create();
        $reportB->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtB->id])->toArray());

        $response = $this->actingAs($operator)->get(route('operator.monitoring'));

        $response->assertStatus(200);
        $response->assertSee($reportA->report_number);
        $response->assertSee($reportB->report_number);
    }

    public function test_operator_provinsi_filter_by_district()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $districtA = District::factory()->create(['name' => 'District A']);
        $districtB = District::factory()->create(['name' => 'District B']);

        $reportA = Report::factory()->create();
        $reportA->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id])->toArray());

        $reportB = Report::factory()->create();
        $reportB->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtB->id])->toArray());

        $response = $this->actingAs($operator)->get(route('operator.monitoring', ['district_id' => $districtA->id]));

        $response->assertStatus(200);
        $response->assertSee($reportA->report_number);
        $response->assertDontSee($reportB->report_number);
    }

    public function test_operator_provinsi_filter_empty_district()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $districtA = District::factory()->create(['name' => 'District A']);
        $districtB = District::factory()->create(['name' => 'District B']);

        $reportA = Report::factory()->create();
        $reportA->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id])->toArray());

        $response = $this->actingAs($operator)->get(route('operator.monitoring', ['district_id' => $districtB->id]));

        $response->assertStatus(200);
        $response->assertDontSee($reportA->report_number);
        $response->assertSee('Tidak ada laporan ditemukan');
    }

    public function test_operator_provinsi_invalid_district_id()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $response = $this->actingAs($operator)->get(route('operator.monitoring', ['district_id' => 9999]));

        // Should return a validation error redirect
        $response->assertStatus(302);
        $response->assertSessionHasErrors('district_id');
    }

    public function test_operator_provinsi_filter_by_district_and_search()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $districtA = District::factory()->create(['name' => 'District A']);

        $reportA1 = Report::factory()->create();
        $reportA1->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id, 'name' => 'Ahmad'])->toArray());

        $reportA2 = Report::factory()->create();
        $reportA2->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id, 'name' => 'Budi'])->toArray());

        $response = $this->actingAs($operator)->get(route('operator.monitoring', [
            'district_id' => $districtA->id,
            'search' => 'Ahmad'
        ]));

        $response->assertStatus(200);
        $response->assertSee($reportA1->report_number);
        $response->assertDontSee($reportA2->report_number);
    }

    public function test_operator_provinsi_filter_by_district_and_status()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $districtA = District::factory()->create(['name' => 'District A']);
        
        $statusPending = ReportStatus::where('status_name', 'Pending')->first();
        $statusDiproses = ReportStatus::where('status_name', 'Diproses')->first();

        $reportA1 = Report::factory()->create(['report_status_id' => $statusPending->id]);
        $reportA1->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id])->toArray());

        $reportA2 = Report::factory()->create(['report_status_id' => $statusDiproses->id]);
        $reportA2->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id])->toArray());

        $response = $this->actingAs($operator)->get(route('operator.monitoring', [
            'district_id' => $districtA->id,
            'status' => 'Pending'
        ]));

        $response->assertStatus(200);
        $response->assertSee($reportA1->report_number);
        $response->assertDontSee($reportA2->report_number);
    }

    public function test_operator_provinsi_pagination_preserves_query()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $operator = User::factory()->create(['role_id' => $operatorRole->id]);

        $districtA = District::factory()->create(['name' => 'District A']);

        // Create 15 reports for District A (paginate 10)
        for ($i = 0; $i < 15; $i++) {
            $report = Report::factory()->create();
            $report->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtA->id])->toArray());
        }

        $response = $this->actingAs($operator)->get(route('operator.monitoring', [
            'district_id' => $districtA->id
        ]));

        $response->assertStatus(200);
        // Ensure pagination link contains district_id
        $response->assertSee('district_id=' . $districtA->id);
    }

    public function test_sub_operator_cannot_bypass_isolation()
    {
        $subOperatorRole = Role::where('role_name', 'Sub Operator')->first();
        $districtA = District::factory()->create(['name' => 'District A']);
        $districtB = District::factory()->create(['name' => 'District B']);
        
        $subOperator = User::factory()->create(['role_id' => $subOperatorRole->id, 'district_id' => $districtA->id]);

        $reportB = Report::factory()->create();
        $reportB->deceased()->create(\App\Models\Deceased::factory()->make(['district_id' => $districtB->id])->toArray());

        // Try to access district B reports on antrean
        // Since antrean does not accept district_id parameter and hardcodes isolation in getReportsQueryForUser,
        // it shouldn't show it even if we pass it, but let's test isolation anyway.
        $response = $this->actingAs($subOperator)->get(route('sub_operator.antrean', ['district_id' => $districtB->id]));

        $response->assertStatus(200);
        $response->assertDontSee($reportB->report_number);
    }
}
