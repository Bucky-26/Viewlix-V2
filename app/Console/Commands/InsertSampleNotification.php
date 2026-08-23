<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class InsertSampleNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:notify {userId} {--title=} {--message=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert a sample notification into Firebase for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $title = $this->option('title') ?? 'Welcome to Viewlix!';
        $message = $this->option('message') ?? 'Thanks for joining. Start exploring movies and TV shows now.';

        $credentials = \App\Services\FirebaseService::getCredentials();
        
        if (!$credentials) {
            $this->error('Firebase credentials not found in .env or storage!');
            return 1;
        }

        try {
            $factory = (new Factory)
                ->withServiceAccount($credentials)
                ->withDatabaseUri(config('services.firebase.database_url'));
            
            $database = $factory->createDatabase();
            
            // Sample notifications
            $notifications = [
                [
                    'title' => 'Welcome to Viewlix!',
                    'message' => 'Thanks for joining. Start exploring movies and TV shows now.',
                    'timestamp' => (time() - 60) * 1000, // 1 minute ago
                    'read' => false,
                    'image' => null,
                    'link' => '/',
                ],
                [
                    'title' => 'New Release: Arcane Season 2',
                    'message' => 'The highly anticipated second season is now available to watch!',
                    'timestamp' => (time() - 3600) * 1000, // 1 hour ago
                    'read' => false,
                    'image' => 'https://image.tmdb.org/t/p/w200/fqldf2t8ztc9aiwn3k6mlX3tvRT.jpg',
                    'link' => '/tv/94605',
                ],
                [
                    'title' => 'Trending Now: Dune Part Two',
                    'message' => 'Dune Part Two is trending! Check it out.',
                    'timestamp' => (time() - 7200) * 1000, // 2 hours ago
                    'read' => true,
                    'image' => 'https://image.tmdb.org/t/p/w200/8b8R8l88Qje9dn9OE8PY05Nxl1X.jpg',
                    'link' => '/movie/693134',
                ],
            ];

            $notifRef = $database->getReference("notifications/{$userId}");
            
            foreach ($notifications as $notif) {
                $notifRef->push($notif);
            }

            $this->info("✅ Inserted " . count($notifications) . " sample notifications for user: {$userId}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
