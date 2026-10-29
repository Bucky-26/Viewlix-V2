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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->text('resolution_notes')->nullable()->after('status');
            $table->string('resolution_reason', 100)->nullable()->after('resolution_notes');
            $table->timestamp('resolved_at')->nullable()->after('resolution_reason');
            $table->foreignId('resolved_by')->nullable()->after('resolved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['resolution_notes', 'resolution_reason', 'resolved_at', 'resolved_by']);
        });
    }
};
