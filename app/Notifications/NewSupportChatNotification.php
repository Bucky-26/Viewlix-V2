<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewSupportChatNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $messageData,
        public string $sessionId
    ) {
    }

    /**
     * Delivery channels
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Mail representation of notification
     */
    public function toMail($notifiable)
    {
        $senderName = $this->messageData['senderName'] ?? 'Guest User';
        $senderEmail = $this->messageData['senderEmail'] ?? 'No email provided';
        $text = $this->messageData['text'] ?? '';
        $adminUrl = url('/admin/support');

        return (new MailMessage)
            ->subject('🔔 New Live Support Chat: ' . $senderName)
            ->greeting('Hello Admin,')
            ->line("You received a new live support message while offline from **{$senderName}** ({$senderEmail}).")
            ->line('**Session ID:** `' . $this->sessionId . '`')
            ->line('**Message:**')
            ->line('"' . $text . '"')
            ->action('Open Live Support Console', $adminUrl)
            ->line('You can reply directly to the user in real-time from the Admin Support console.')
            ->salutation('Viewlix Support Dispatcher');
    }
}
