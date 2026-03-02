<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->change();
            $table->foreignId('office_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable(false)->change();
            $table->foreignId('office_id')->nullable(false)->change();
        });
    }
};
