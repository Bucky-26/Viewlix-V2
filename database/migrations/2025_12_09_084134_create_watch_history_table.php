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
        Schema::create('watch_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('media_id'); // TMDB ID
            $table->enum('media_type', ['movie', 'tv', 'anime']);
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->unsignedInteger('season')->nullable(); // For TV shows
            $table->unsignedInteger('episode')->nullable(); // For TV shows
            $table->float('progress')->default(0); // Percentage watched (0-100)
            $table->unsignedInteger('watched_time')->default(0); // Seconds watched
            $table->unsignedInteger('duration')->default(0); // Total duration in seconds
            $table->timestamps();

            // Index for quick lookups
            $table->index(['user_id', 'media_id', 'media_type']);
            // Unique constraint for movie/full show, or specific episode
            $table->unique(['user_id', 'media_id', 'media_type', 'season', 'episode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_history');
    }
};
