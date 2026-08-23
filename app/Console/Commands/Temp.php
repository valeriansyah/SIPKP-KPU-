<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('temp')]
#[Description('Command description')]
class Temp extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
