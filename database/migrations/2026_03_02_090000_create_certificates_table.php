<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('internship_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('nim')->nullable();
            $table->string('predikat')->nullable();
            $table->date('certificate_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
