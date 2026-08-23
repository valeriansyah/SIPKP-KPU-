<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$role = Role::where('role_name', 'Sub Operator')->first();
$users = User::where('role_id', $role->id)->with('district')->get();
foreach ($users as $user) {
    echo $user->district->name.' | '.$user->email."\n";
}
