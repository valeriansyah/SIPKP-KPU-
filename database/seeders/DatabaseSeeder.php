<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ReportStatusSeeder::class,
            DistrictSeeder::class,
            DocumentTypeSeeder::class,
            ActorSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}