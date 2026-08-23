<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $request = Request::create('/api/test', 'POST');
    $request->setMethod('POST');
    $validator = Validator::make(['document_type_id' => 999], ['document_type_id' => 'exists:document_types,id']);
    $validator->validate();
} catch (Exception $e) {
    echo $e->getMessage()."\n".$e->getTraceAsString();
}
