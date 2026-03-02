<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * For SQLite: Rebuild table with nullable project_id
     */
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            // SQLite workaround: use PRAGMA to allow null in project_id
            DB::statement('PRAGMA foreign_keys=off;');
            
            // Create temporary table with nullable project_id
            DB::statement('CREATE TABLE tickets_temp AS SELECT * FROM tickets;');
            
            // Drop old table
            DB::statement('DROP TABLE tickets;');
            
            // Recreate table with nullable project_id
            DB::statement('
                CREATE TABLE tickets (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    project_id INTEGER NULL,
                    ticket_status_id INTEGER NULL,
                    name TEXT NOT NULL,
                    description TEXT NULL,
                    due_date TEXT NULL,
                    uuid TEXT NOT NULL,
                    epic_id INTEGER NULL,
                    created_by INTEGER NULL,
                    priority TEXT DEFAULT "flexible",
                    start_date TEXT NULL,
                    approval_status TEXT DEFAULT "pending",
                    approved_by INTEGER NULL,
                    approved_at TEXT NULL,
                    attachment TEXT NULL,
                    status TEXT DEFAULT "belum",
                    created_at TEXT NULL,
                    updated_at TEXT NULL
                )
            ');
            
            // Copy data back
            DB::statement('INSERT INTO tickets SELECT * FROM tickets_temp;');
            
            // Drop temp table
            DB::statement('DROP TABLE tickets_temp;');
            
            DB::statement('PRAGMA foreign_keys=on;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
