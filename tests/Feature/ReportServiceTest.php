<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use App\Models\ReportStatus;
use App\Models\Report;
use App\Models\Deceased;
use Illuminate\Support\Facades\Hash;
use App\Services\ReportService;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $operatorRole;
    protected $subOperatorRole;
    protected $pelaporRole;
    
    protected $pendingStatus;
    protected $perluPerbaikanStatus;

    protected $districtPalembang;
    protected $districtLahat;

    protected $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->operatorRole = Role::firstOrCreate(['role_name' => 'Operator']);
        $this->subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);
        $this->pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);

        $this->pendingStatus = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $this->perluPerbaikanStatus = ReportStatus::firstOrCreate(['status_name' => 'Perlu Perbaikan']);

        $this->districtPalembang = District::firstOrCreate(['name' => 'Palembang', 'code' => '1671']);
        $this->districtLahat = District::firstOrCreate(['name' => 'Lahat', 'code' => '1604']);

        $this->reportService = app(ReportService::class);
    }

    protected function createUser($role, $districtId = null)
    {
        return User::create([
            'full_name' => 'Test ' . $role->role_name,
            'username' => 'testuser_' . uniqid(),
            'phone_number' => '08123456789',
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id,
            'district_id' => $districtId,
            'is_active' => true
        ]);
    }

    // CREATE REPORT
    public function test_pelapor_creates_report_successfully()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $this->actingAs($pelapor);

        $data = [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Create',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_place' => 'Palembang',
            'death_date' => '2023-01-01',
            'documents' => [
                1 => \Illuminate\Http\UploadedFile::fake()->create('doc1.pdf', 100),
                2 => \Illuminate\Http\UploadedFile::fake()->create('doc2.pdf', 100),
                3 => \Illuminate\Http\UploadedFile::fake()->create('doc3.pdf', 100),
                6 => \Illuminate\Http\UploadedFile::fake()->create('doc6.pdf', 100),
            ]
        ];

        $response = $this->postJson('/reports', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'user_id', 'report_status_id', 'report_number', 
            'deceased' => ['id', 'name', 'district_id']
        ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => $pelapor->id,
            'report_status_id' => $this->pendingStatus->id,
        ]);

        $this->assertDatabaseHas('deceased', [
            'name' => 'Almarhum Create',
            'district_id' => $this->districtPalembang->id
        ]);
    }

    public function test_report_number_format_is_correct()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        $this->actingAs($pelapor);

        $data = [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Create',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_place' => 'Palembang',
            'death_date' => '2023-01-01',
            'documents' => [
                1 => \Illuminate\Http\UploadedFile::fake()->create('doc1.pdf', 100),
                2 => \Illuminate\Http\UploadedFile::fake()->create('doc2.pdf', 100),
                3 => \Illuminate\Http\UploadedFile::fake()->create('doc3.pdf', 100),
                6 => \Illuminate\Http\UploadedFile::fake()->create('doc6.pdf', 100),
            ]
        ];

        $response = $this->postJson('/reports', $data);
        $reportNumber = $response->json('report_number');
        
        $today = now()->format('Ymd');
        $this->assertStringStartsWith('SIPKP-' . $today . '-', $reportNumber);
    }

    // OWNERSHIP
    public function test_pelapor_can_only_get_own_reports()
    {
        $pelapor1 = $this->createUser($this->pelaporRole);
        $pelapor2 = $this->createUser($this->pelaporRole);

        // create using service directly
        $this->reportService->createReport($pelapor1, [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Pelapor 1',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_place' => 'Palembang',
            'death_date' => '2023-01-01',
        ]);

        $this->reportService->createReport($pelapor2, [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Pelapor 2',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_place' => 'Palembang',
            'death_date' => '2023-01-01',
        ]);

        $this->actingAs($pelapor1);
        $response = $this->getJson('/reports');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $this->assertEquals('Almarhum Pelapor 1', $response->json('0.deceased.name'));
    }

    // DISTRICT
    public function test_sub_operator_only_gets_reports_in_own_district()
    {
        $subOpPlg = $this->createUser($this->subOperatorRole, $this->districtPalembang->id);
        $pelapor = $this->createUser($this->pelaporRole);

        $this->reportService->createReport($pelapor, [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum PLG',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
        ]);

        $this->reportService->createReport($pelapor, [
            'district_id' => $this->districtLahat->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Lahat',
            'gender' => 'Laki-laki',
            'birth_place' => 'Lahat',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
        ]);

        $this->actingAs($subOpPlg);
        $response = $this->getJson('/reports');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $this->assertEquals('Almarhum PLG', $response->json('0.deceased.name'));
    }

    // UPDATE
    public function test_owner_can_update_report_if_status_allows()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        
        $report = $this->reportService->createReport($pelapor, [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum Salah',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
        ]);

        // Manually set to Perlu Perbaikan to allow update via Policy
        $report->update(['report_status_id' => $this->perluPerbaikanStatus->id]);

        $this->actingAs($pelapor);

        $updateData = [
            'name' => 'Almarhum Benar'
        ];

        $response = $this->putJson('/reports/' . $report->id, $updateData);

        $response->assertStatus(200);
        
        // Status should be back to Pending
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => $this->pendingStatus->id,
        ]);

        $this->assertDatabaseHas('deceased', [
            'report_id' => $report->id,
            'name' => 'Almarhum Benar'
        ]);
    }

    public function test_owner_cannot_update_report_if_status_not_allows()
    {
        $pelapor = $this->createUser($this->pelaporRole);
        
        $report = $this->reportService->createReport($pelapor, [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
        ]);

        // Status is currently Pending

        $this->actingAs($pelapor);

        $updateData = [
            'name' => 'Almarhum Update'
        ];

        $response = $this->putJson('/reports/' . $report->id, $updateData);

        // Policy denies it
        $response->assertStatus(403);
    }

    public function test_other_user_cannot_update_report()
    {
        $pelapor1 = $this->createUser($this->pelaporRole);
        $pelapor2 = $this->createUser($this->pelaporRole);
        
        $report = $this->reportService->createReport($pelapor1, [
            'district_id' => $this->districtPalembang->id,
            'nik' => '1234567890123456',
            'family_card_number' => '1234567890123456',
            'name' => 'Almarhum',
            'gender' => 'Laki-laki',
            'birth_place' => 'Palembang',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Merdeka',
            'death_date' => '2023-01-01',
        ]);
        $report->update(['report_status_id' => $this->perluPerbaikanStatus->id]);

        $this->actingAs($pelapor2);

        $updateData = [
            'name' => 'Almarhum Update'
        ];

        $response = $this->putJson('/reports/' . $report->id, $updateData);
        $response->assertStatus(403);
    }
}
