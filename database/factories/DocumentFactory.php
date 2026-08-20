<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'report_id' => Report::factory(),
            'document_type_id' => DocumentType::inRandomOrder()->first()->id ?? 1,
            'file_path' => 'dummy/sipkp/document-' . fake()->uuid() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(100000, 5000000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
