<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\DocumentType;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase7IE2EWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_pelapor_can_create_report_and_sub_operator_can_verify_it()
    {
        // 1. Setup Roles
        $pelaporRole = Role::firstOrCreate(['role_name' => 'Pelapor']);
        $subOperatorRole = Role::firstOrCreate(['role_name' => 'Sub Operator']);

        // 2. Setup Districts and Statuses
        $district = District::firstOrCreate(
            ['name' => 'Palembang'],
            ['code' => '16.71']
        );
        $statusPending = ReportStatus::firstOrCreate(['status_name' => 'Pending']);
        $statusDisetujui = ReportStatus::firstOrCreate(['status_name' => 'Disetujui']);

        // 3. Create Users
        $pelapor = User::factory()->create([
            'role_id' => $pelaporRole->id,
            'district_id' => null,
        ]);

        $subOperator = User::factory()->create([
            'role_id' => $subOperatorRole->id,
            'district_id' => $district->id,
        ]);

        // Setup Document Types in DB
        $doc1 = DocumentType::firstOrCreate(['id' => 1, 'name' => 'Surat Keterangan Kematian', 'is_required' => true]);
        $doc2 = DocumentType::firstOrCreate(['id' => 2, 'name' => 'KTP Almarhum', 'is_required' => true]);
        $doc3 = DocumentType::firstOrCreate(['id' => 3, 'name' => 'Kartu Keluarga', 'is_required' => true]);
        $doc6 = DocumentType::firstOrCreate(['id' => 6, 'name' => 'KTP Pelapor', 'is_required' => true]);

        // 4. ACTOR 1 (Pelapor) creates a report
        $reportData = [
            'nik' => '1671'.$this->faker->numerify('############'),
            'family_card_number' => '1671'.$this->faker->numerify('############'),
            'name' => 'Test Almarhum E2E',
            'gender' => 'Laki-laki',
            'district_id' => $district->id,
            'birth_place' => 'Palembang',
            'birth_date' => '1950-01-01',
            'death_date' => now()->subDays(2)->format('Y-m-d'),
            'address' => 'Jl. Test E2E No. 123',
            'agreement' => 'on',
            'documents' => [
                1 => UploadedFile::fake()->create('surat_kematian.pdf', 100),
                2 => UploadedFile::fake()->create('ktp_almarhum.jpg', 100),
                3 => UploadedFile::fake()->create('kk.jpg', 100),
                6 => UploadedFile::fake()->create('ktp_pelapor.jpg', 100),
            ],
        ];

        $response = $this->actingAs($pelapor)
            ->post(route('pelapor.laporan.store'), $reportData);

        // Assert report is created and redirected
        $response->assertRedirect(route('pelapor.laporan.index'));
        $response->assertSessionHas('success');

        // Check DB
        $this->assertDatabaseHas('reports', [
            'user_id' => $pelapor->id,
            'report_status_id' => $statusPending->id,
        ]);

        $report = Report::where('user_id', $pelapor->id)->first();
        $this->assertNotNull($report);

        $this->assertDatabaseHas('deceased', [
            'nik' => $reportData['nik'],
            'name' => $reportData['name'],
        ]);

        // 5. ACTOR 2 (Sub Operator) sees the report in antrean
        $this->actingAs($subOperator)
            ->get(route('sub_operator.antrean'))
            ->assertStatus(200)
            ->assertSee($report->report_number);

        // 6. ACTOR 2 (Sub Operator) verifies the report (Disetujui)
        $verifyData = [
            'decision' => 'disetujui',
            'notes' => 'Dokumen lengkap, disetujui via E2E test.',
        ];

        $verifyResponse = $this->actingAs($subOperator)
            ->post(route('sub_operator.laporan.verifikasi', $report->id), $verifyData);

        // Assert redirect back to antrean with success
        $verifyResponse->assertRedirect(route('sub_operator.antrean'));
        $verifyResponse->assertSessionHas('success', 'Laporan berhasil diverifikasi.');

        // Check DB state updated
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'report_status_id' => $statusDisetujui->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $subOperator->id,
            'activity' => 'Report Verification',
        ]);
    }
}
