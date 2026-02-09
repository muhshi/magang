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
        Schema::table('tickets', function (Blueprint $table) {
            // Priority field with 3 levels
            $table->enum('priority', ['urgent', 'important', 'flexible'])
                ->default('flexible')
                ->after('description');

            // Start date for when work begins
            $table->date('start_date')
                ->nullable()
                ->after('priority');

            // Approval workflow fields
            $table->enum('approval_status', ['pending', 'approved'])
                ->default('pending')
                ->after('due_date');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('approval_status');

            $table->timestamp('approved_at')
                ->nullable()
                ->after('approved_by');

            // Attachment field for file uploads
            $table->string('attachment')
                ->nullable()
                ->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'priority',
                'start_date',
                'approval_status',
                'approved_by',
                'approved_at',
                'attachment',
            ]);
        });
    }
};
