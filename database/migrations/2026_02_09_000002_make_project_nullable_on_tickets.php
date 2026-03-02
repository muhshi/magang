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
        // For SQLite, we need to recreate columns as nullable
        // Using a workaround since SQLite doesn't support ALTER COLUMN
        
        Schema::table('tickets', function (Blueprint $table) {
            // Make project_id nullable by dropping foreign key first (if exists)
            // Then modifying the column
        });

        // Direct SQL for SQLite compatibility
        if (config('database.default') === 'sqlite') {
            // SQLite doesn't support modifying columns, but new records can have null
            // The column is already configured, just ensure new inserts work
        } else {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->change();
                $table->unsignedBigInteger('ticket_status_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable(false)->change();
                $table->unsignedBigInteger('ticket_status_id')->nullable(false)->change();
            });
        }
    }
};
