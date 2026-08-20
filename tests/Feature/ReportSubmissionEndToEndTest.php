<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use App\Models\Report;

class ReportSubmissionEndToEndTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $pelaporUser;
    protected $pelaporUser2;
    protected $subOperatorUser;
    protected $operatorUser;
    protected $districtA;
    protected $districtB;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'ReportStatusSeeder']);
        $this->artisan('db:seed', ['--class' => 'DocumentTypeSeeder']);

        $this->districtA = District::create(['name' => 'Palembang', 'code' => '1671']);
        $this->districtB = District::create(['name' => 'Ogan Ilir', 'code' => '1602']);
        
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();
        $subOpRole = Role::where('role_name', 'Sub Operator')->first();
        $opRole = Role::where('role_name', 'Operator Provinsi')->first();

        $this->pelaporUser = User::factory()->create(['role_id' => $pelaporRole->id]);
        $this->pelaporUser2 = User::factory()->create(['role_id' => $pelaporRole->id]);
        $this->subOperatorUser = User::factory()->create([
            'role_id' => $subOpRole->id,
            'district_id' => $this->districtA->id
        ]);
        $this->operatorUser = User::factory()->create(['role_id' => $opRole->id]);

        Storage::fake('public');
    }

    protected function getValidPayload($overrides = [])
    {
        return array_merge([
            'district_id' => $this->districtA->id,
            'nik' => '1671012345678901',
            'family_card_number' => '1671012345678902',
            'name' => 'Fulan bin Fulan',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1980-01-01',
            'address' => 'Jl. Merdeka No 1',
            'death_date' => '2026-08-01',
            'agreement' => 'on',
            'documents' => [
                1 => UploadedFile::fake()->create('suket.pdf', 1000, 'application/pdf'),
                2 => UploadedFile::fake()->create('ktp_alm.jpg', 1000, 'image/jpeg'),
                3 => UploadedFile::fake()->create('kk.png', 1000, 'image/png'),
                6 => UploadedFile::fake()->create('ktp_pelapor.pdf', 1000, 'application/pdf'),
            ],
        ], $overrides);
    }

    public function test_end_to_end_successful_submission()
    {
        $payload = $this->getValidPayload();

        $response = $this->actingAs($this->pelaporUser)
            ->post(route('pelapor.laporan.store'), $payload);

        $response->assertRedirect(route('pelapor.laporan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('reports', 1);
        $this->assertDatabaseCount('deceased', 1);
        $this->assertDatabaseCount('documents', 4);
        $this->assertDatabaseCount('audit_logs', 5); // 1 create + 4 docs

        $report = Report::first();
        
        // Ownership Validation
        $this->assertEquals($this->pelaporUser->id, $report->user_id);
        
        // Initial status validation
        $this->assertEquals('Pending', $report->reportStatus->status_name);

        // Sub operator visibility validation
        $subOpResponse = $this->actingAs($this->subOperatorUser)->get(route('sub_operator.antrean'));
        $subOpResponse->assertSee($report->report_number);

        // Detail page validation
        $detailResponse = $this->actingAs($this->pelaporUser)->get(route('pelapor.laporan.show', $report->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($report->report_number);
    }

    public function test_prevents_duplicate_nik_submission()
    {
        $payload = $this->getValidPayload();

        // First submission
        $this->actingAs($this->pelaporUser)->post(route('pelapor.laporan.store'), $payload);
        $this->assertDatabaseCount('reports', 1);

        // Second submission with same NIK
        $response = $this->actingAs($this->pelaporUser)->post(route('pelapor.laporan.store'), $payload);
        
        $response->assertSessionHasErrors(['nik']);
        $this->assertDatabaseCount('reports', 1); // Should not increase
    }

    public function test_idor_pelapor_cannot_view_others_report()
    {
        $payload = $this->getValidPayload();
        $this->actingAs($this->pelaporUser)->post(route('pelapor.laporan.store'), $payload);
        
        $report = Report::first();

        $response = $this->actingAs($this->pelaporUser2)->get(route('pelapor.laporan.show', $report->id));
        $response->assertStatus(403);
    }

    public function test_sub_operator_cannot_view_outside_district()
    {
        // Sub operator is in district A
        // Create report in district B
        $payload = $this->getValidPayload(['district_id' => $this->districtB->id]);
        $this->actingAs($this->pelaporUser)->post(route('pelapor.laporan.store'), $payload);

        $report = Report::first();

        // Check queue
        $response = $this->actingAs($this->subOperatorUser)->get(route('sub_operator.antrean'));
        $response->assertDontSee($report->report_number);

        // Check direct access
        $response = $this->actingAs($this->subOperatorUser)->get(route('sub_operator.laporan.show', $report->id));
        $response->assertStatus(403);
    }

    public function test_operator_provinsi_can_access_monitoring()
    {
        $response = $this->actingAs($this->operatorUser)->get(route('operator.monitoring'));
        $response->assertStatus(200);
        $response->assertViewIs('operator.monitoring');
    }
}
