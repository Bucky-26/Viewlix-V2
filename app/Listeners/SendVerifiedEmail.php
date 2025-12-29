<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use App\Notifications\AccountVerified;
use Illuminate\Support\Facades\Cache;


class SendVerifiedEmail
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Prevent duplicate notifications being sent if the Verified event is
        // accidentally dispatched more than once in quick succession.
        $cacheKey = 'verified-email-sent-' . $user->id;

        // Attempt to set a short-lived cache key. If it already exists, skip sending.
        $added = Cache::add($cacheKey, true, 60); // keep for 60 seconds
        if (! $added) {
            // Another process recently sent the notification; skip.
            return;
        }

        try {
            $user->notify(new AccountVerified());
        } catch (\Throwable $e) {
            \Log::warning('Failed to send account verified notification: ' . $e->getMessage());
            // Remove cache key so it can be retried later
            Cache::forget($cacheKey);
        }
    }
}
