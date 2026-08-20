<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use App\Models\Deceased;
use App\Models\Document;
use App\Models\ReportStatus;
use App\Models\District;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $pelapors = User::whereHas('role', function($q) {
            $q->where('role_name', 'Pelapor');
        })->get();

        if ($pelapors->isEmpty()) {
            return;
        }

        $statuses = ReportStatus::all();
        $districts = District::all();
        $documentTypes = DocumentType::all();

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
        
        // Ensure "pelapor1@sipkp.local" has some specific reports in Palembang for testing
        $pelapor1 = User::where('email', 'pelapor1@sipkp.local')->first();
        $palembang = District::where('name', 'Palembang')->first();
        $pending = ReportStatus::where('status_name', 'Pending')->first();
        $diproses = ReportStatus::where('status_name', 'Diproses')->first();
        
        if ($pelapor1 && $palembang) {
            $r1 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $pending->id]);
            Deceased::factory()->create(['report_id' => $r1->id, 'district_id' => $palembang->id]);
            
            $r2 = Report::factory()->create(['user_id' => $pelapor1->id, 'report_status_id' => $diproses->id]);
            Deceased::factory()->create(['report_id' => $r2->id, 'district_id' => $palembang->id]);
        }
    }
}
