<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public string $agentName = 'Support Team'
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
        $ticketId = $this->ticket->ticket_id;
        $subject = $this->ticket->subject;
        $notes = $this->ticket->resolution_notes ?: 'Your inquiry has been addressed and resolved.';
        $reason = $this->ticket->resolution_reason ? "({$this->ticket->resolution_reason})" : '';
        $chatUrl = url('/?open_ticket=' . urlencode($ticketId));

        return (new MailMessage)
            ->subject("✅ Ticket Resolved: [{$ticketId}] {$subject}")
            ->greeting('Hi ' . ($this->ticket->name ?: 'there') . ',')
            ->line("Your support ticket **{$ticketId}** has been marked as **Resolved** by {$this->agentName} {$reason}.")
            ->line("**Resolution Details:**")
            ->line('"' . $notes . '"')
            ->action('View Ticket Details in Viewlix', $chatUrl)
            ->line('If you still need assistance or if this issue recurs, you can re-open the ticket or reach out anytime via live chat.')
            ->salutation('Warm regards, Viewlix Support Team');
    }
}
