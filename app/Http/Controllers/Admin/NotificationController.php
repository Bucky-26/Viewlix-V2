<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use App\Models\User;

class NotificationController extends Controller
{
    protected $database;
    protected $messaging;

    public function __construct()
    {
        $credentials = config('services.firebase.credentials');
        
        if ($credentials && file_exists($credentials)) {
            $factory = (new Factory)->withServiceAccount($credentials);
            $this->database = $factory->createDatabase();
            $this->messaging = $factory->createMessaging();
        }
    }

    /**
     * Show the notifications management page
     */
    public function index()
    {
        // Get recent notifications from Firebase
        $notifications = [];
        
        if ($this->database) {
            try {
                $snapshot = $this->database->getReference('admin_notifications')
                    ->orderByChild('created_at')
                    ->limitToLast(20)
                    ->getSnapshot();
                
                $notifications = $snapshot->getValue() ?? [];
                
                // Sort by created_at descending
                if (!empty($notifications)) {
                    uasort($notifications, function($a, $b) {
                        return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
                    });
                }
            } catch (\Exception $e) {
                // Silently fail
            }
        }

        return view('admin.notifications.index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Show the create notification form
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Send a notification to all users
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'type' => 'required|in:announcement,new_content,update',
            'link' => 'nullable|string|max:255',
            'image' => 'nullable|url|max:500',
            'send_push' => 'nullable|boolean',
        ]);

        if (!$this->database) {
            return back()->with('error', 'Firebase is not configured.');
        }

        $notificationId = uniqid('notif_');
        $timestamp = time() * 1000; // JavaScript timestamp

        $notificationData = [
            'id' => $notificationId,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'link' => $request->link ?? null,
            'image' => $request->image ?? null,
            'created_at' => $timestamp,
            'sent_by' => auth()->user()->name,
        ];

        try {
            // Store in admin_notifications for history
            $this->database->getReference('admin_notifications/' . $notificationId)
                ->set($notificationData);

            // Get all users and send to each
            $users = User::all();
            $userNotifData = array_merge($notificationData, ['read' => false]);

            foreach ($users as $user) {
                $this->database->getReference('notifications/' . $user->id . '/' . $notificationId)
                    ->set($userNotifData);
            }

            // Send FCM push notifications if requested
            $pushCount = 0;
            if ($request->send_push && $this->messaging) {
                $pushCount = $this->sendPushNotifications($request->title, $request->message, $request->link, $request->image);
            }

            $message = 'Notification sent to ' . $users->count() . ' users.';
            if ($request->send_push) {
                $message .= ' Push sent to ' . $pushCount . ' devices.';
            }

            return redirect()->route('admin.notifications')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Send FCM push notifications to all registered devices
     */
    protected function sendPushNotifications($title, $body, $link = null, $image = null)
    {
        if (!$this->messaging || !$this->database) {
            return 0;
        }

        try {
            // Get all FCM tokens from Firebase
            $tokensSnapshot = $this->database->getReference('fcm_tokens')->getSnapshot();
            $tokensData = $tokensSnapshot->getValue() ?? [];

            $tokens = [];
            foreach ($tokensData as $userId => $tokenInfo) {
                if (isset($tokenInfo['token'])) {
                    $tokens[] = $tokenInfo['token'];
                }
            }

            if (empty($tokens)) {
                return 0;
            }

            // Create the notification
            $notification = Notification::create($title, $body);
            
            if ($image) {
                $notification = $notification->withImageUrl($image);
            }

            // Web push config
            $webPushConfig = WebPushConfig::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/logo.png',
                    'badge' => '/logo.png',
                    'image' => $image,
                    'click_action' => $link ?? '/',
                    'data' => [
                        'link' => $link ?? '/',
                    ],
                ],
                'fcm_options' => [
                    'link' => $link ?? '/',
                ],
            ]);

            $successCount = 0;
            $invalidTokens = [];

            // Send to each token (FCM has a limit of 500 per batch)
            $chunks = array_chunk($tokens, 500);
            
            foreach ($chunks as $chunk) {
                $messages = [];
                
                foreach ($chunk as $token) {
                    $messages[] = CloudMessage::withTarget('token', $token)
                        ->withNotification($notification)
                        ->withWebPushConfig($webPushConfig);
                }

                $sendReport = $this->messaging->sendAll($messages);
                $successCount += $sendReport->successes()->count();

                // Collect invalid tokens to remove
                foreach ($sendReport->failures()->getItems() as $failure) {
                    if ($failure->error() && str_contains($failure->error()->getMessage(), 'not a valid FCM registration token')) {
                        $invalidTokens[] = $failure->target()->value();
                    }
                }
            }

            // Remove invalid tokens
            foreach ($tokensData as $userId => $tokenInfo) {
                if (isset($tokenInfo['token']) && in_array($tokenInfo['token'], $invalidTokens)) {
                    $this->database->getReference('fcm_tokens/' . $userId)->remove();
                }
            }

            return $successCount;
        } catch (\Exception $e) {
            \Log::error('FCM Push Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        if (!$this->database) {
            return back()->with('error', 'Firebase is not configured.');
        }

        try {
            // Remove from admin_notifications
            $this->database->getReference('admin_notifications/' . $id)->remove();

            // Note: We don't remove from user notifications - they stay until user clears them

            return back()->with('success', 'Notification deleted from history.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
}
