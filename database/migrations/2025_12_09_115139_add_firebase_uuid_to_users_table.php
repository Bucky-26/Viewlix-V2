<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('firebase_uuid')->unique()->nullable()->after('id');
        });
        
        // Generate UUIDs for existing users
        $users = \App\Models\User::whereNull('firebase_uuid')->get();
        foreach ($users as $user) {
            $user->firebase_uuid = Str::uuid()->toString();
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('firebase_uuid');
        });
    }
};
