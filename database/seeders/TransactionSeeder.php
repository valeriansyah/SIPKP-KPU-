<?php

namespace Database\Seeders;

use App\Models\Deceased;
use App\Models\District;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\ReportVerification;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $pelapors = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Pelapor');
        })->get();

        if ($pelapors->isEmpty()) {
            return;
        }

        $documentTypes = DocumentType::all();

        // Ensure "pelapor1@sipkp.local" has some specific reports in Palembang for testing
        // We do this BEFORE the idempotency check so they are always generated if missing.
        // To make it idempotent, we check if pelapor1 already has reports.
        $pelapor1 = User::where('email', 'pelapor1@sipkp.local')->first();
        if ($pelapor1 && $pelapor1->reports()->count() === 0) {
            $palembang = District::where('name', 'Palembang')->first();
            $prabumulih = District::where('name', 'Prabumulih')->first();

            $subOpPlg = User::where('email', 'subop.plg@sipkp.local')->first();

            $statuses = ReportStatus::all()->keyBy('status_name');

            if ($palembang && $subOpPlg) {
                // REPORT A: Pending (Palembang)
                $r1 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $statuses['Pending']->id]);
                Deceased::factory()->create(['report_id' => $r1->id, 'district_id' => $palembang->id]);
                Document::factory()->create(['report_id' => $r1->id, 'document_type_id' => $documentTypes->random()->id]);

                // REPORT B: Perlu Perbaikan (Palembang)
                $r2 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $statuses['Perlu Perbaikan']->id]);
                Deceased::factory()->create(['report_id' => $r2->id, 'district_id' => $palembang->id]);
                ReportVerification::create([
                    'report_id' => $r2->id,
                    'verified_by' => $subOpPlg->id,
                    'decision' => 'perlu_perbaikan',
                    'notes' => 'Dokumen KK kurang jelas, mohon upload ulang.',
                ]);

                // REPORT C: Disetujui (Palembang)
                $r3 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $statuses['Disetujui']->id]);
                Deceased::factory()->create(['report_id' => $r3->id, 'district_id' => $palembang->id]);
                ReportVerification::create([
                    'report_id' => $r3->id,
                    'verified_by' => $subOpPlg->id,
                    'decision' => 'disetujui',
                    'notes' => 'Data valid dan lengkap.',
                ]);

                // REPORT D: Ditolak (Palembang)
                $r4 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $statuses['Ditolak']->id]);
                Deceased::factory()->create(['report_id' => $r4->id, 'district_id' => $palembang->id]);
                ReportVerification::create([
                    'report_id' => $r4->id,
                    'verified_by' => $subOpPlg->id,
                    'decision' => 'ditolak',
                    'notes' => 'Almarhum tidak terdaftar di wilayah ini.',
                ]);

                // REPORT E: Pending (Prabumulih - untuk tes isolasi district)
                if ($prabumulih) {
                    $r5 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $statuses['Pending']->id]);
                    Deceased::factory()->create(['report_id' => $r5->id, 'district_id' => $prabumulih->id]);
                }
            }
        }

        // Idempotency check: prevent duplicate random dummy data
        if (! Document::where('file_path', 'like', 'dummy/sipkp/%')->exists()) {
            $statuses = ReportStatus::all();
            $districts = District::all();

            // Create 30 random reports
            for ($i = 0; $i < 30; $i++) {
                $pelapor = $pelapors->random();
                $status = $statuses->random();
                $district = $districts->random();

                $report = Report::factory()->create([
                    'user_id' => $pelapor->id,
                    'report_status_id' => $status->id,
                ]);

                Deceased::factory()->create([
                    'report_id' => $report->id,
                    'district_id' => $district->id,
                ]);

                // Add 1-2 documents per report
                $docCount = rand(1, 2);
                for ($j = 0; $j < $docCount; $j++) {
                    Document::factory()->create([
                        'report_id' => $report->id,
                        'document_type_id' => $documentTypes->random()->id,
                    ]);
                }
            }
        }
    }
}
