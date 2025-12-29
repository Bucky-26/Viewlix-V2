<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use App\Notifications\AccountVerified;

class SendVerifiedEmail
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        try {
            $user->notify(new AccountVerified());
        } catch (\Throwable $e) {
            \Log::warning('Failed to send account verified notification: ' . $e->getMessage());
        }
    }
}
