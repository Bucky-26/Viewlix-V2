<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('base_url')->nullable();
            $table->text('movie_url_pattern')->nullable();
            $table->text('tv_url_pattern')->nullable();
            $table->text('anime_url_pattern')->nullable();
            $table->boolean('supports_movie')->default(true);
            $table->boolean('supports_tv')->default(true);
            $table->boolean('supports_anime')->default(true);
            $table->boolean('is_default_movie')->default(false);
            $table->boolean('is_default_tv')->default(false);
            $table->boolean('is_default_anime')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // Seed initial sources matching the existing setup
        DB::table('video_sources')->insert([
            [
                'name' => 'Viewlix [VLX]',
                'base_url' => 'https://monitor.viewlix.online/vlx',
                'movie_url_pattern' => 'https://monitor.viewlix.online/vlx/movie/{id}',
                'tv_url_pattern' => 'https://monitor.viewlix.online/vlx/tv/{id}/{season}/{episode}',
                'anime_url_pattern' => null,
                'supports_movie' => true,
                'supports_tv' => true,
                'supports_anime' => true,
                'is_default_movie' => true,
                'is_default_tv' => true,
                'is_default_anime' => false,
                'is_active' => true,
                'priority' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Viewlix (ad-free)',
                'base_url' => 'https://content.viewlix.online/embed',
                'movie_url_pattern' => 'https://content.viewlix.online/embed/movie/{id}',
                'tv_url_pattern' => 'https://content.viewlix.online/embed/tv/{id}/{season}/{episode}',
                'anime_url_pattern' => 'https://content.viewlix.online/embed/tv/{id}/{season}/{episode}',
                'supports_movie' => true,
                'supports_tv' => true,
                'supports_anime' => true,
                'is_default_movie' => false,
                'is_default_tv' => false,
                'is_default_anime' => true,
                'is_active' => true,
                'priority' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VidLink',
                'base_url' => 'https://vidlink.pro',
                'movie_url_pattern' => 'https://vidlink.pro/movie/{id}',
                'tv_url_pattern' => 'https://vidlink.pro/tv/{id}/{season}/{episode}',
                'anime_url_pattern' => 'https://vidlink.pro/tv/{id}/{season}/{episode}',
                'supports_movie' => true,
                'supports_tv' => true,
                'supports_anime' => true,
                'is_default_movie' => false,
                'is_default_tv' => false,
                'is_default_anime' => false,
                'is_active' => true,
                'priority' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FilMex (Ad-Free) Suggested',
                'base_url' => 'https://fmovies4u.com',
                'movie_url_pattern' => 'https://fmovies4u.com/embed/movie/{id}?autoPlay=true&hideTitle=true&fullscreenButton=true',
                'tv_url_pattern' => 'https://fmovies4u.com/embed/tv/{id}/{season}/{episode}?autoPlay=true&hideTitle=true',
                'anime_url_pattern' => 'https://fmovies4u.com/embed/tv/{id}/{season}/{episode}?autoPlay=true&hideTitle=true',
                'supports_movie' => true,
                'supports_tv' => true,
                'supports_anime' => true,
                'is_default_movie' => false,
                'is_default_tv' => false,
                'is_default_anime' => false,
                'is_active' => true,
                'priority' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vidsrcme',
                'base_url' => 'https://vidsrcme.ru',
                'movie_url_pattern' => 'https://vidsrcme.ru/embed/movie?tmdb={id}',
                'tv_url_pattern' => 'https://vidsrcme.ru/embed/tv?tmdb={id}&season={season}&episode={episode}',
                'anime_url_pattern' => 'https://vidsrcme.ru/embed/tv?tmdb={id}&season={season}&episode={episode}',
                'supports_movie' => true,
                'supports_tv' => true,
                'supports_anime' => true,
                'is_default_movie' => false,
                'is_default_tv' => false,
                'is_default_anime' => false,
                'is_active' => true,
                'priority' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VidRock',
                'base_url' => 'https://vidrock.net',
                'movie_url_pattern' => 'https://vidrock.net/movie/{id}',
                'tv_url_pattern' => null,
                'anime_url_pattern' => null,
                'supports_movie' => true,
                'supports_tv' => false,
                'supports_anime' => false,
                'is_default_movie' => false,
                'is_default_tv' => false,
                'is_default_anime' => false,
                'is_active' => true,
                'priority' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_sources');
    }
};
