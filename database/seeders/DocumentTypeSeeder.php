<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('document_types')->upsert(
            [
                [
                    'name' => 'Surat Keterangan Kematian',
                    'description' => 'Dokumen resmi yang menerangkan bahwa almarhum telah meninggal dunia.',
                    'is_required' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'KTP Almarhum',
                    'description' => 'Kartu Tanda Penduduk milik almarhum sebagai dokumen identitas.',
                    'is_required' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Kartu Keluarga (KK)',
                    'description' => 'Kartu Keluarga yang digunakan untuk mendukung verifikasi identitas almarhum.',
                    'is_required' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Surat Pengantar RT/RW',
                    'description' => 'Surat pengantar dari RT/RW sebagai dokumen pendukung pelaporan.',
                    'is_required' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Surat Visum',
                    'description' => 'Dokumen visum yang dapat digunakan apabila tersedia atau relevan dengan kondisi kematian.',
                    'is_required' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'KTP Pelapor',
                    'description' => 'Kartu Tanda Penduduk milik pelapor sebagai dokumen identitas pelapor.',
                    'is_required' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Akta Kelahiran Almarhum',
                    'description' => 'Akta kelahiran almarhum sebagai dokumen pendukung identifikasi.',
                    'is_required' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Foto Almarhum',
                    'description' => 'Foto almarhum sebagai dokumen pendukung laporan.',
                    'is_required' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['name'],
            ['description', 'is_required', 'updated_at']
        );
    }
}
