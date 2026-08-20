<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                [
                    'role_name' => 'Pelapor',
                    'description' => 'Pengguna yang membuat dan mengirimkan laporan kematian.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'role_name' => 'Sub Operator',
                    'description' => 'Operator yang bertanggung jawab terhadap satu Kabupaten/Kota.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'role_name' => 'Operator Provinsi',
                    'description' => 'Operator tingkat provinsi yang mengelola sistem secara keseluruhan.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['role_name'],
            ['description', 'updated_at']
        );
    }
}