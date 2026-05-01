<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'type' => 'popup',
            'title' => 'Welcome to Viewlix',
            'description' => 'Discover the ultimate streaming experience with our vast collection of movies, TV shows, and anime. Enjoy HD quality content on all devices.',
            'image' => null,
            'icon' => '🎬',
            'button_text' => 'Get Started',
            'button_url' => '/explore',
            'hotlinks' => null,
            'is_active' => true,
        ]);

        Banner::create([
            'type' => 'popup',
            'title' => 'Download Now',
            'description' => 'Download the Viewlix mobile app and watch your favorite content anytime, anywhere.',
            'image' => null,
            'icon' => '📱',
            'button_text' => 'Download App',
            'button_url' => '/download',
            'hotlinks' => null,
            'is_active' => false,
        ]);

        Banner::create([
            'type' => 'header',
            'title' => 'Special Offer: 50% Off Premium',
            'description' => 'Limited time offer for new users!',
            'image' => null,
            'icon' => '🎉',
            'button_text' => null,
            'button_url' => null,
            'hotlinks' => [
                ['text' => 'Learn More', 'url' => '/pricing', 'target' => '_self'],
                ['text' => 'Subscribe Now', 'url' => '/subscribe', 'target' => '_self'],
            ],
            'is_active' => true,
        ]);

        // Example with SVG icon
        // To use SVG, pass it as a string like:
        // 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="..."/></svg>'
        
        Banner::create([
            'type' => 'header',
            'title' => 'New Features Available',
            'description' => 'Check out our latest updates',
            'image' => null,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
            'button_text' => null,
            'button_url' => null,
            'hotlinks' => [
                ['text' => 'View Updates', 'url' => '/updates', 'target' => '_self'],
            ],
            'is_active' => false,
        ]);
    }
}
