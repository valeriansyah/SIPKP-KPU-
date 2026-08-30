<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_revision_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->enum('revision_type', ['data', 'document']);
            $table->string('field_name')->nullable();
            $table->foreignId('document_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
            
            // Optional: Index for faster lookup
            $table->index(['report_id', 'is_resolved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_revision_items');
    }
};
