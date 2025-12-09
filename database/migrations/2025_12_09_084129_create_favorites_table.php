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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('media_id'); // TMDB ID
            $table->enum('media_type', ['movie', 'tv', 'anime']);
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->float('vote_average')->nullable();
            $table->string('release_date')->nullable();
            $table->timestamps();

            // Unique constraint: user can only favorite same media once
            $table->unique(['user_id', 'media_id', 'media_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
