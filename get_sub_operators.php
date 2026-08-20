<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$role = App\Models\Role::where('role_name', 'Sub Operator')->first();
$users = App\Models\User::where('role_id', $role->id)->with('district')->get();
foreach($users as $user) {
    echo $user->district->name . ' | ' . $user->email . "\n";
}
