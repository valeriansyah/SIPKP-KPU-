<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- REPORT STATUSES ---\n";
foreach (\App\Models\ReportStatus::all() as $status) {
    echo "ID: {$status->id} | Name: {$status->status_name} | Slug: " . \Illuminate\Support\Str::slug($status->status_name, '_') . "\n";
}

echo "\n--- RECENT REPORTS ---\n";
$report = \App\Models\Report::with('reportStatus')->orderBy('id', 'desc')->first();
echo "Latest Report ID: {$report->id} | Number: {$report->report_number} | Status: {$report->reportStatus->status_name}\n";

// Let's test the validation logic manually
echo "\n--- VALIDATION TEST ---\n";
$validator = \Illuminate\Support\Facades\Validator::make(
    ['decision' => 'approve'],
    ['decision' => 'required|string|in:diproses,perlu_perbaikan,disetujui,ditolak']
);

if ($validator->fails()) {
    echo "Validation FAILS for 'approve':\n";
    print_r($validator->errors()->all());
} else {
    echo "Validation PASSES for 'approve'\n";
}
