<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix data lama: field tanggal berisi "-" diubah jadi null
        DB::table('tickets')
            ->where('start_date', '-')
            ->update(['start_date' => null]);

        DB::table('tickets')
            ->where('due_date', '-')
            ->update(['due_date' => null]);
    }

    public function down(): void
    {
        // Tidak perlu di-revert
    }
};
