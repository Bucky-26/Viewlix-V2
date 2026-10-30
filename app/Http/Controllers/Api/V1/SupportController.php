<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SupportTicket;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupportController extends BaseApiController
{
    public function __construct(protected FirebaseService $firebase)
    {
    }

    /**
     * Get list of support tickets for the authenticated user.
     * GET /api/v1/support/tickets
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tickets = SupportTicket::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($ticket) {
                return [
                    'id'                 => $ticket->id,
                    'ticket_id'          => $ticket->ticket_id,
                    'session_id'         => $ticket->session_id,
                    'subject'            => $ticket->subject,
                    'category'           => $ticket->category,
                    'priority'           => $ticket->priority,
                    'status'             => $ticket->status,
                    'message'            => $ticket->message,
                    'reopen_requested'   => (bool) $ticket->reopen_requested,
                    'resolved_at'        => $ticket->resolved_at?->toIso8601String(),
                    'created_at'         => $ticket->created_at?->toIso8601String(),
                ];
            });

        return $this->success($tickets);
    }

    /**
     * Submit a new support ticket.
     * POST /api/v1/support/tickets
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'message'  => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $ticketId = SupportTicket::generateTicketId();
        $cleanTktId = str_replace('-', '_', $ticketId);
        $ticketSessionId = 'sess_tkt_' . $cleanTktId;

        $ticket = SupportTicket::create([
            'ticket_id'  => $ticketId,
            'session_id' => $ticketSessionId,
            'user_id'    => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'category'   => $validated['category'] ?? 'general',
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'priority'   => $validated['priority'] ?? 'normal',
            'status'     => 'open',
        ]);

        // Sync to Firebase Realtime Database
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $timestamp = time() * 1000;

                    // 1. Store ticket in support_tickets
                    $db->getReference("support_tickets/{$ticketId}")->set([
                        'ticketId'  => $ticketId,
                        'sessionId' => $ticketSessionId,
                        'userId'    => $ticket->user_id,
                        'userName'  => $ticket->name,
                        'userEmail' => $ticket->email,
                        'category'  => $ticket->category,
                        'subject'   => $ticket->subject,
                        'message'   => $ticket->message,
                        'priority'  => $ticket->priority,
                        'status'    => $ticket->status,
                        'createdAt' => $timestamp,
                    ]);

                    // 2. Initialize chat session thread
                    $db->getReference("support_chats/{$ticketSessionId}/meta")->set([
                        'sessionId'     => $ticketSessionId,
                        'isTicket'      => true,
                        'ticketId'      => $ticketId,
                        'ticketSubject' => $ticket->subject,
                        'ticketStatus'  => 'open',
                        'category'      => $ticket->category,
                        'priority'      => $ticket->priority,
                        'userId'        => $ticket->user_id,
                        'userName'      => $ticket->name,
                        'userEmail'     => $ticket->email,
                        'lastMessage'   => "Ticket {$ticketId}: {$ticket->subject}",
                        'updatedAt'     => $timestamp,
                        'status'        => 'open',
                    ]);

                    // 3. Post initial message
                    $initMsgId = 'msg_tkt_init_' . Str::random(8);
                    $db->getReference("support_chats/{$ticketSessionId}/messages/{$initMsgId}")->set([
                        'id'          => $initMsgId,
                        'sender'      => 'user',
                        'senderName'  => $ticket->name,
                        'senderEmail' => $ticket->email,
                        'userId'      => $ticket->user_id,
                        'text'        => $ticket->message,
                        'timestamp'   => $timestamp,
                        'status'      => 'delivered',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Firebase support ticket sync failed via Mobile API: ' . $e->getMessage());
            }
        }

        return $this->success([
            'ticket'     => $ticket,
            'ticket_id'  => $ticketId,
            'session_id' => $ticketSessionId,
        ], 'Support ticket submitted successfully', 201);
    }

    /**
     * Get details and messages for a specific support ticket.
     * GET /api/v1/support/tickets/{ticketId}
     */
    public function show(string $ticketId, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $ticket = SupportTicket::where('user_id', $user->id)
            ->where(function ($q) use ($ticketId) {
                $q->where('ticket_id', $ticketId)->orWhere('id', $ticketId);
            })
            ->first();

        if (!$ticket) {
            return $this->error('Support ticket not found.', 404);
        }

        $messages = [];
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $rawMsgs = $db->getReference("support_chats/{$ticket->session_id}/messages")->getValue();
                    if (is_array($rawMsgs)) {
                        $messages = array_values($rawMsgs);
                        usort($messages, fn($a, $b) => ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Firebase fetch ticket messages failed: ' . $e->getMessage());
            }
        }

        return $this->success([
            'ticket'   => $ticket,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a reply message in a support ticket.
     * POST /api/v1/support/tickets/{ticketId}/messages
     */
    public function sendMessage(string $ticketId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:5000'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $ticket = SupportTicket::where('user_id', $user->id)
            ->where(function ($q) use ($ticketId) {
                $q->where('ticket_id', $ticketId)->orWhere('id', $ticketId);
            })
            ->first();

        if (!$ticket) {
            return $this->error('Support ticket not found.', 404);
        }

        if ($ticket->status === 'closed') {
            return $this->error('This ticket is closed. You must request reopening it to send messages.', 403);
        }

        $timestamp = (int) (microtime(true) * 1000);
        $msgId = 'msg_m_' . time() . '_' . Str::random(6);

        $messageData = [
            'id'          => $msgId,
            'sender'      => 'user',
            'senderName'  => $user->name,
            'senderEmail' => $user->email,
            'userId'      => $user->id,
            'text'        => trim($validated['text']),
            'timestamp'   => $timestamp,
            'status'      => 'sent',
        ];

        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $db->getReference("support_chats/{$ticket->session_id}/messages/{$msgId}")->set($messageData);
                    $db->getReference("support_chats/{$ticket->session_id}/meta")->update([
                        'lastMessage' => Str::limit($messageData['text'], 100),
                        'updatedAt'   => $timestamp,
                        'status'      => 'active',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Firebase send ticket message failed: ' . $e->getMessage());
            }
        }

        return $this->success($messageData, 'Message sent successfully', 201);
    }

    /**
     * Request reopening a resolved or closed ticket.
     * POST /api/v1/support/tickets/{ticketId}/reopen
     */
    public function requestReopen(string $ticketId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $ticket = SupportTicket::where('user_id', $user->id)
            ->where(function ($q) use ($ticketId) {
                $q->where('ticket_id', $ticketId)->orWhere('id', $ticketId);
            })
            ->first();

        if (!$ticket) {
            return $this->error('Support ticket not found.', 404);
        }

        $ticket->update([
            'reopen_requested'    => true,
            'reopen_reason'       => $validated['reason'],
            'reopen_requested_at' => now(),
        ]);

        return $this->success($ticket, 'Reopen request submitted successfully');
    }
}

