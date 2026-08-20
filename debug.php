<?php

require __DIR__.'/vendor/autoload.php'; 
$app = require_once __DIR__.'/bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

try { 
    $request = \Illuminate\Http\Request::create('/api/test', 'POST'); 
    $request->setMethod('POST'); 
    $validator = \Illuminate\Support\Facades\Validator::make(['document_type_id' => 999], ['document_type_id' => 'exists:document_types,id']); 
    $validator->validate(); 
} catch (\Exception $e) { 
    echo $e->getMessage() . "\n" . $e->getTraceAsString(); 
}
