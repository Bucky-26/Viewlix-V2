<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SupportReplyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $replyData,
        public string $recipientName
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
        $agentName = $this->replyData['senderName'] ?? 'Support Team';
        $replyText = $this->replyData['text'] ?? '';
        $chatUrl = url('/?open_chat=1');

        return (new MailMessage)
            ->subject('💬 New Reply from Viewlix Support')
            ->greeting('Hi ' . ($this->recipientName ?: 'there') . ',')
            ->line("**{$agentName}** from Viewlix Support just replied to your conversation:")
            ->line('"' . $replyText . '"')
            ->action('Continue Conversation in Viewlix', $chatUrl)
            ->line('If you have any further questions, feel free to respond directly in the live chat bubble.')
            ->salutation('Warm regards, Viewlix Support Team');
    }
}
