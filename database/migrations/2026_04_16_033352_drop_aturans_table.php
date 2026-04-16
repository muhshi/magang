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
        Schema::dropIfExists('aturans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('aturans', function (Blueprint $table) {
            $table->id();
            $table->enum('aturan', ['WFA', 'Banned']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->text('alasan')->nullable();
            $table->string('bukti')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
};
