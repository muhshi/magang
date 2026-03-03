<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->string('program_studi')->nullable()->after('education_level');
            $table->string('fakultas')->nullable()->after('program_studi');
            $table->string('nim')->nullable()->after('fakultas');
        });
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropColumn(['program_studi', 'fakultas', 'nim']);
        });
    }
};
