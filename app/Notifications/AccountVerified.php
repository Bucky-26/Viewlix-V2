<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountVerified extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Viewlix account is verified')
                    ->greeting('Hi ' . ($notifiable->name ?? 'there') . ',')
                    ->line('We have verified your email address. Your account is now fully activated.')
                    ->line('You can now enjoy all features that require a verified account.')
                    ->action('Go to Viewlix', url('/'))
                    ->salutation('Thanks for joining ' . config('app.name', 'Viewlix'));
    }
}
