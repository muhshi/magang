<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik logbook
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete(); // jika dari sistem
            $table->enum('source', ['system', 'manual'])->default('manual');
            $table->date('tanggal_pengisian');
            $table->string('nama_pegawai'); // pemberi tugas
            $table->text('deskripsi_tugas');
            $table->enum('status', ['belum', 'proses', 'revisi', 'selesai'])->default('belum');
            $table->string('lampiran')->nullable();
            $table->timestamps();
        });

        Schema::create('logbook_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_users');
        Schema::dropIfExists('logbooks');
    }
};
