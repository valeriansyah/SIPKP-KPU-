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
        Schema::dropIfExists('otp_codes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->string('otp');
            $table->string('purpose');
            $table->timestamp('expired_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }
};
