<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            // Fix data lama: field tanggal berisi "-" diubah jadi null
            DB::table('tickets')
                ->where('start_date', '-')
                ->update(['start_date' => null]);
        } catch (\Exception $e) {
            // Ignore if mysql strict mode throws Invalid datetime format error
        }

        try {
            DB::table('tickets')
                ->where('due_date', '-')
                ->update(['due_date' => null]);
        } catch (\Exception $e) {
            // Ignore if mysql strict mode throws Invalid datetime format error
        }
    }

    public function down(): void
    {
        // Tidak perlu di-revert
    }
};
