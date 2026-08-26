<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\ReportStatusSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardIntegrationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, ReportStatusSeeder::class, DistrictSeeder::class]);
    }

    public function test_pelapor_can_see_own_dashboard_and_only_own_stats()
    {
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();
        $statusPending = ReportStatus::where('status_name', 'Pending')->first();

        $pelaporA = User::factory()->create(['role_id' => $pelaporRole->id]);
        $pelaporB = User::factory()->create(['role_id' => $pelaporRole->id]);

        // Pelapor A has 2 reports
        Report::factory()->count(2)->create([
            'user_id' => $pelaporA->id,
            'report_status_id' => $statusPending->id,
        ]);

        // Pelapor B has 3 reports
        Report::factory()->count(3)->create([
            'user_id' => $pelaporB->id,
            'report_status_id' => $statusPending->id,
        ]);

        // Act & Assert Pelapor A
        $responseA = $this->actingAs($pelaporA)->get('/pelapor/dashboard');
        $responseA->assertStatus(200);
        $responseA->assertViewHas('metrics', function ($metrics) {
            return $metrics['total'] === 2 && $metrics['pending'] === 2;
        });
        $responseA->assertViewHas('recentReports', function ($reports) use ($pelaporA) {
            return $reports->count() === 2 && $reports->first()->user_id === $pelaporA->id;
        });

        // Act & Assert Pelapor B
        $responseB = $this->actingAs($pelaporB)->get('/pelapor/dashboard');
        $responseB->assertStatus(200);
        $responseB->assertViewHas('metrics', function ($metrics) {
            return $metrics['total'] === 3 && $metrics['pending'] === 3;
        });
    }

    public function test_pelapor_empty_state()
    {
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();
        $pelapor = User::factory()->create(['role_id' => $pelaporRole->id]);

        $response = $this->actingAs($pelapor)->get('/pelapor/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['total'] === 0;
        });
        $response->assertViewHas('recentReports', function ($reports) {
            return $reports->isEmpty();
        });
    }

    public function test_sub_operator_sees_empty_queue()
    {
        $subOpRole = Role::where('role_name', 'Sub Operator')->first();
        $subOp = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => District::first()->id,
        ]);

        $response = $this->actingAs($subOp)->get('/sub-operator/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('queue', function ($queue) {
            return $queue->isEmpty();
        });
    }

    public function test_pelapor_sees_sub_operator_contact_by_district()
    {
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();
        $subOpRole = Role::where('role_name', 'Sub Operator')->first();

        $districtA = District::first();
        $districtB = District::where('id', '!=', $districtA->id)->first();

        // Sub Operator for District A
        $subOpA = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $districtA->id,
            'full_name' => 'Sub Op District A',
            'phone_number' => '08111111111',
        ]);

        // Sub Operator for District B (no phone)
        $subOpB = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $districtB->id,
            'full_name' => 'Sub Op District B',
            'phone_number' => null,
        ]);

        // Pelapor in District A
        $pelaporA = User::factory()->create([
            'role_id' => $pelaporRole->id,
            'district_id' => $districtA->id,
        ]);

        // TEST 3: Pelapor A sees Sub Op A contact
        $responseA = $this->actingAs($pelaporA)->get('/pelapor/dashboard');
        $responseA->assertStatus(200);
        $responseA->assertViewHas('subOperator', function ($subOp) use ($subOpA) {
            return $subOp->id === $subOpA->id;
        });
        $responseA->assertSee('Sub Op District A');
        $responseA->assertSee('08111111111');
        
        // TEST 4: Pelapor A does NOT see Sub Op B contact
        $responseA->assertDontSee('Sub Op District B');

        // Pelapor in District B
        $pelaporB = User::factory()->create([
            'role_id' => $pelaporRole->id,
            'district_id' => $districtB->id,
        ]);

        // TEST 5: Dashboard is normal if phone is null (Sub Op B)
        $responseB = $this->actingAs($pelaporB)->get('/pelapor/dashboard');
        $responseB->assertStatus(200);
        $responseB->assertSee('Sub Op District B');
        $responseB->assertSee('Nomor telepon belum tersedia');

        // Pelapor in District C (no Sub Operator)
        $districtC = District::factory()->create();
        $pelaporC = User::factory()->create([
            'role_id' => $pelaporRole->id,
            'district_id' => $districtC->id,
        ]);

        $responseC = $this->actingAs($pelaporC)->get('/pelapor/dashboard');
        $responseC->assertStatus(200);
        $responseC->assertSee('Kontak Belum Tersedia');
        $responseC->assertSee('Kontak Sub Operator wilayah Anda belum tersedia');
    }

    public function test_sub_operator_can_see_only_own_district_stats()
    {
        $subOpRole = Role::where('role_name', 'Sub Operator')->first();
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();

        $districtPalembang = District::where('name', 'Palembang')->first();
        $districtPrabumulih = District::where('name', 'Prabumulih')->first();

        $subOpPalembang = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $districtPalembang->id,
        ]);

        $subOpPrabumulih = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $districtPrabumulih->id,
        ]);

        $pelapor = User::factory()->create(['role_id' => $pelaporRole->id]);

        // Create 2 reports in Palembang
        $reportsPalembang = Report::factory()->count(2)->create(['user_id' => $pelapor->id, 'report_status_id' => 1]);
        foreach ($reportsPalembang as $r) {
            Deceased::factory()->create(['report_id' => $r->id, 'district_id' => $districtPalembang->id]);
        }

        // Create 3 reports in Prabumulih
        $reportsPrabumulih = Report::factory()->count(3)->create(['user_id' => $pelapor->id, 'report_status_id' => 1]);
        foreach ($reportsPrabumulih as $r) {
            Deceased::factory()->create(['report_id' => $r->id, 'district_id' => $districtPrabumulih->id]);
        }

        // Act & Assert Sub Operator Palembang
        // Test query param manipulation attempt
        $responseA = $this->actingAs($subOpPalembang)->get('/sub-operator/dashboard?district_id='.$districtPrabumulih->id);
        $responseA->assertStatus(200);
        $responseA->assertViewHas('metrics', function ($metrics) {
            return $metrics['total'] === 2; // Should not be 3 or 5, strictly 2
        });
        $responseA->assertViewHas('queue', function ($queue) {
            return $queue->total() === 2;
        });

        // Act & Assert Sub Operator Prabumulih
        $responseB = $this->actingAs($subOpPrabumulih)->get('/sub-operator/dashboard');
        $responseB->assertStatus(200);
        $responseB->assertViewHas('metrics', function ($metrics) {
            return $metrics['total'] === 3;
        });
    }

    public function test_operator_can_see_global_stats()
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();

        $operator = User::factory()->create(['role_id' => $operatorRole->id]);
        $pelapor = User::factory()->create(['role_id' => $pelaporRole->id]);

        $reports = Report::factory()->count(5)->create(['user_id' => $pelapor->id, 'report_status_id' => 1]);
        foreach ($reports as $r) {
            Deceased::factory()->create(['report_id' => $r->id, 'district_id' => 1]);
        }

        $response = $this->actingAs($operator)->get('/operator/dashboard');
        $response->assertStatus(200);

        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['total'] === 5;
        });

        $response->assertViewHas('districtStatistics', function ($stats) {
            return $stats !== null;
        });

        $response->assertViewHas('activities');
        $response->assertViewHas('recentReports');
    }

    public function test_unauthenticated_cannot_access_dashboard()
    {
        $this->get('/pelapor/dashboard')->assertRedirect('/login');
        $this->get('/sub-operator/dashboard')->assertRedirect('/login');
        $this->get('/operator/dashboard')->assertRedirect('/login');
    }

    public function test_cross_role_access_is_denied()
    {
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();
        $pelapor = User::factory()->create(['role_id' => $pelaporRole->id]);

        // Pelapor trying to access Operator and Sub Operator
        $this->actingAs($pelapor)->get('/operator/dashboard')->assertForbidden();
        $this->actingAs($pelapor)->get('/sub-operator/dashboard')->assertForbidden();
    }
}
