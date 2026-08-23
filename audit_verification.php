<?php

use App\Models\Report;
use App\Models\ReportStatus;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "--- REPORT STATUSES ---\n";
foreach (ReportStatus::all() as $status) {
    echo "ID: {$status->id} | Name: {$status->status_name} | Slug: ".Str::slug($status->status_name, '_')."\n";
}

echo "\n--- RECENT REPORTS ---\n";
$report = Report::with('reportStatus')->orderBy('id', 'desc')->first();
echo "Latest Report ID: {$report->id} | Number: {$report->report_number} | Status: {$report->reportStatus->status_name}\n";

// Let's test the validation logic manually
echo "\n--- VALIDATION TEST ---\n";
$validator = Validator::make(
    ['decision' => 'approve'],
    ['decision' => 'required|string|in:diproses,perlu_perbaikan,disetujui,ditolak']
);

if ($validator->fails()) {
    echo "Validation FAILS for 'approve':\n";
    print_r($validator->errors()->all());
} else {
    echo "Validation PASSES for 'approve'\n";
}
