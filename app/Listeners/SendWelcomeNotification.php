<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Kreait\Firebase\Messaging\WebPushConfig;
use App\Notifications\WelcomeNotification;

class SendWelcomeNotification
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        \Log::info('SendWelcomeNotification: handling Registered for user id ' . ($user->id ?? 'unknown') . ' (' . ($user->email ?? 'unknown') . ')');

        $credentials = config('services.firebase.credentials');
        $databaseUrl = config('services.firebase.database_url');

        // Always attempt to send a welcome email regardless of Firebase
        try {
            $user->notify(new WelcomeNotification());
        } catch (\Throwable $e) {
            \Log::warning('Failed to send welcome email: ' . $e->getMessage());
        }

        // If Firebase is not configured or the service account file doesn't
        // exist, skip the Firebase-specific actions but do not abort the
        // welcome email flow.
        if (!$credentials || !file_exists($credentials)) {
            \Log::info('Firebase credentials not found; skipping welcome push/DB notification.');
            return;
        }

        try {
            $factory = (new Factory)
                ->withServiceAccount($credentials)
                ->withDatabaseUri($databaseUrl);

            $database = $factory->createDatabase();
            $messaging = $factory->createMessaging();

            $firebaseUuid = $user->getFirebaseUuid();
            $notificationId = uniqid('welcome_');
            $timestamp = time() * 1000;

            $title = 'Welcome to ' . config('app.name', 'Viewlix');
            $message = 'Hi ' . ($user->name ?: 'there') . ", welcome to Viewlix — enjoy discovering great movies, TV shows, and anime!";

            $data = [
                'id' => $notificationId,
                'title' => $title,
                'message' => $message,
                'type' => 'welcome',
                'link' => '/',
                'image' => null,
                'created_at' => $timestamp,
                'sent_by' => 'System',
                'read' => false,
            ];

            // Store notification for the user
            $database->getReference('notifications/' . $firebaseUuid . '/' . $notificationId)
                ->set($data);

            // Welcome email already sent above; continue with push logic.

            // Attempt to send FCM push to this user's tokens (if any)
            try {
                $tokensSnapshot = $database->getReference('fcmTokens/' . $firebaseUuid)->getSnapshot();
                $tokensData = $tokensSnapshot->getValue() ?? [];
                $tokens = [];

                foreach ($tokensData as $tokenId => $tokenInfo) {
                    if (is_array($tokenInfo) && isset($tokenInfo['token'])) {
                        $tokens[] = $tokenInfo['token'];
                    }
                }

                if (!empty($tokens) && $messaging) {
                    $fcmNotif = FcmNotification::create($title, $message)->withImageUrl('/logo.png');
                    $webPushConfig = WebPushConfig::fromArray([
                        'notification' => [
                            'title' => $title,
                            'body' => $message,
                            'icon' => '/logo.png',
                            'badge' => '/logo.png',
                            'click_action' => '/',
                            'data' => ['link' => '/'],
                        ],
                    ]);

                    $messages = [];
                    foreach ($tokens as $token) {
                        $messages[] = CloudMessage::withTarget('token', $token)
                            ->withNotification($fcmNotif)
                            ->withWebPushConfig($webPushConfig);
                    }

                    if (!empty($messages)) {
                        // sendAll may throw; ignore failures silently for welcome
                        $messaging->sendAll($messages);
                    }
                }
            } catch (\Throwable $e) {
                // don't break registration on push failure
                \Log::warning('Welcome push failed: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            // If Firebase fails, don't interrupt user registration
            \Log::warning('SendWelcomeNotification failed: ' . $e->getMessage());
        }
    }
}
