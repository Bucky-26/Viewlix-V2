<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
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
                    ->subject('Welcome to ' . config('app.name', 'Viewlix'))
                    ->greeting('Hi ' . ($notifiable->name ?? 'there') . ',')
                    ->line('Welcome to ' . config('app.name', 'Viewlix') . '! We\'re excited to have you on board.')
                    ->line('Explore movies, TV shows, and anime — and create your favorites to pick up where you left off.')
                    ->action('Start discovering', url('/'))
                    ->salutation('Thanks for joining ' . config('app.name', 'Viewlix'));
    }
}
