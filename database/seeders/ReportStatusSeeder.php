<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('report_statuses')->upsert(
            [
                [
                    'status_name' => 'Pending',
                    'description' => 'Laporan baru yang belum diproses.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_name' => 'Diproses',
                    'description' => 'Laporan sedang dalam proses verifikasi.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_name' => 'Perlu Perbaikan',
                    'description' => 'Laporan dikembalikan untuk diperbaiki oleh pelapor.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_name' => 'Disetujui',
                    'description' => 'Laporan telah disetujui setelah proses verifikasi.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_name' => 'Ditolak',
                    'description' => 'Laporan ditolak setelah proses verifikasi.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['status_name'],
            ['description', 'updated_at']
        );
    }
}
