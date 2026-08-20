<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deceased', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->unique()->constrained('reports')->restrictOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->string('nik', 16)->index();
            $table->string('family_card_number', 16)->index();
            $table->string('name', 100);
            $table->string('gender', 20);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->text('address');
            $table->string('death_place', 255)->nullable();
            $table->date('death_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deceased');
    }
};
