<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportCreationFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $pelaporUser;

    protected $operatorUser;

    protected $subOperatorUser;

    protected $district;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'ReportStatusSeeder']);
        $this->artisan('db:seed', ['--class' => 'DocumentTypeSeeder']);

        $this->district = District::create(['name' => 'Palembang', 'code' => '1671']);

        $pelaporRole = Role::where('role_name', 'Pelapor')->first();
        $this->pelaporUser = User::factory()->create(['role_id' => $pelaporRole->id]);

        $subOperatorRole = Role::where('role_name', 'Sub Operator')->first();
        $this->subOperatorUser = User::factory()->create([
            'role_id' => $subOperatorRole->id,
            'district_id' => $this->district->id,
        ]);

        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $this->operatorUser = User::factory()->create(['role_id' => $operatorRole->id]);

        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_pelapor_can_view_create_form()
    {
        $response = $this->actingAs($this->pelaporUser)->get(route('pelapor.laporan.create'));
        $response->assertStatus(200);
        $response->assertViewIs('pelapor.laporan.create');
        $response->assertSee('Buat Laporan Kematian Pemilih');
    }

    public function test_operator_and_sub_operator_cannot_view_create_form()
    {
        $response = $this->actingAs($this->operatorUser)->get(route('pelapor.laporan.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->subOperatorUser)->get(route('pelapor.laporan.create'));
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_view_create_form()
    {
        $response = $this->get(route('pelapor.laporan.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_pelapor_can_create_report_with_valid_data()
    {
        $doc1 = UploadedFile::fake()->create('suket.pdf', 1000, 'application/pdf');
        $doc2 = UploadedFile::fake()->create('ktp_alm.jpg', 1000, 'image/jpeg');
        $doc3 = UploadedFile::fake()->create('kk.png', 1000, 'image/png');
        $doc6 = UploadedFile::fake()->create('ktp_pelapor.pdf', 1000, 'application/pdf');

        $payload = [
            'district_id' => $this->district->id,
            'nik' => '1671012345678901',
            'family_card_number' => '1671012345678902',
            'name' => 'Fulan bin Fulan',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1980-01-01',
            'address' => 'Jl. Merdeka No 1',
            'death_date' => '2026-08-01',
            'documents' => [
                1 => $doc1,
                2 => $doc2,
                3 => $doc3,
                6 => $doc6,
            ],
            'agreement' => 'on',
        ];

        $response = $this->actingAs($this->pelaporUser)
            ->post(route('pelapor.laporan.store'), $payload);

        $response->assertRedirect(route('pelapor.laporan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'user_id' => $this->pelaporUser->id,
        ]);

        $report = Report::where('user_id', $this->pelaporUser->id)->first();

        $this->assertEquals('Pending', $report->reportStatus->status_name);

        $this->assertDatabaseHas('deceased', [
            'report_id' => $report->id,
            'nik' => '1671012345678901',
            'district_id' => $this->district->id,
        ]);

        $this->assertDatabaseCount('documents', 4);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->pelaporUser->id,
            'activity' => 'Membuat Laporan',
        ]);

        // File should exist in fake storage
        $documents = $report->documents;
        foreach ($documents as $doc) {
            Storage::disk('local')->assertExists($doc->file_path);
        }
    }

    public function test_pelapor_cannot_create_report_missing_required_documents()
    {
        $payload = [
            'district_id' => $this->district->id,
            'nik' => '1671012345678901',
            'family_card_number' => '1671012345678902',
            'name' => 'Fulan bin Fulan',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1980-01-01',
            'address' => 'Jl. Merdeka No 1',
            'death_date' => '2026-08-01',
            // Missing documents completely
        ];

        $response = $this->actingAs($this->pelaporUser)
            ->post(route('pelapor.laporan.store'), $payload);

        $response->assertSessionHasErrors(['documents']);

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_pelapor_cannot_create_report_with_death_date_before_birth_date()
    {
        $payload = [
            'district_id' => $this->district->id,
            'nik' => '1671012345678901',
            'family_card_number' => '1671012345678902',
            'name' => 'Fulan bin Fulan',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '2000-01-01',
            'address' => 'Jl. Merdeka No 1',
            'death_date' => '1999-01-01', // Invalid: before birth_date
            'documents' => [
                1 => UploadedFile::fake()->create('suket.pdf', 1000, 'application/pdf'),
                2 => UploadedFile::fake()->create('ktp_alm.jpg', 1000, 'image/jpeg'),
                3 => UploadedFile::fake()->create('kk.png', 1000, 'image/png'),
                6 => UploadedFile::fake()->create('ktp_pelapor.pdf', 1000, 'application/pdf'),
            ],
            'agreement' => 'on',
        ];

        $response = $this->actingAs($this->pelaporUser)
            ->post(route('pelapor.laporan.store'), $payload);

        $response->assertSessionHasErrors(['death_date']);

        $this->assertDatabaseCount('reports', 0);
    }
}
