<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- USERS ---\n";
$users = App\Models\User::with('role')->get();
foreach($users as $u) {
    echo "ID: {$u->id} | Role: {$u->role->role_name} | Name: {$u->full_name} | Username: {$u->username} | Email: {$u->email} | Pass Length: " . strlen($u->password) . "\n";
}

echo "\n--- REPORTS ---\n";
$reports = App\Models\Report::with(['reportStatus', 'deceased', 'documents'])->get();
foreach($reports as $r) {
    echo "Report: {$r->report_number} | Status: {$r->reportStatus->status_name} | Docs: " . count($r->documents) . "\n";
}
