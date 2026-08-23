<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('districts')->upsert(
            [
                [
                    'name' => 'Ogan Komering Ulu',
                    'code' => '1601',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Ogan Komering Ilir',
                    'code' => '1602',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Muara Enim',
                    'code' => '1603',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Lahat',
                    'code' => '1604',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Musi Rawas',
                    'code' => '1605',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Musi Banyuasin',
                    'code' => '1606',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Banyuasin',
                    'code' => '1607',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Ogan Komering Ulu Timur',
                    'code' => '1608',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Ogan Komering Ulu Selatan',
                    'code' => '1609',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Ogan Ilir',
                    'code' => '1610',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Empat Lawang',
                    'code' => '1611',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Penukal Abab Lematang Ilir',
                    'code' => '1612',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Musi Rawas Utara',
                    'code' => '1613',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Palembang',
                    'code' => '1671',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Pagar Alam',
                    'code' => '1672',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Lubuklinggau',
                    'code' => '1673',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Prabumulih',
                    'code' => '1674',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['code'],
            ['name', 'updated_at']
        );
    }
}
