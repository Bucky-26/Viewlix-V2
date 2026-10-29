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
            $table->boolean('reopen_requested')->default(false)->after('status');
            $table->text('reopen_reason')->nullable()->after('reopen_requested');
            $table->timestamp('reopen_requested_at')->nullable()->after('reopen_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['reopen_requested', 'reopen_reason', 'reopen_requested_at']);
        });
    }
};
