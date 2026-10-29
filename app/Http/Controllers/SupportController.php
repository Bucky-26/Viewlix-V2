<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function __construct(protected FirebaseService $firebase)
    {
    }

    /**
     * Submit a support ticket (optional formal request)
     */
    public function submitTicket(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Only authenticated users can request support.',
                'requires_auth' => true,
            ], 401);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
            'session_id' => 'required|string|max:128',
        ]);

        $user = Auth::user();
        $ticketId = SupportTicket::generateTicketId();

        // Create dedicated ticket chat session so ticket discussions are isolated from general live chat
        $cleanTktId = str_replace('-', '_', $ticketId);
        $ticketSessionId = 'sess_tkt_' . $cleanTktId;

        $ticket = SupportTicket::create([
            'ticket_id' => $ticketId,
            'session_id' => $ticketSessionId,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'category' => $validated['category'] ?? 'general',
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'] ?? 'normal',
            'status' => 'open',
        ]);

        // Sync to Firebase RTDB for real-time visibility
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $timestamp = time() * 1000;

                    // 1. Store ticket in support_tickets
                    $db->getReference("support_tickets/{$ticketId}")->set([
                        'ticketId' => $ticketId,
                        'sessionId' => $ticket->session_id,
                        'userId' => $ticket->user_id,
                        'userName' => $ticket->name,
                        'userEmail' => $ticket->email,
                        'category' => $ticket->category,
                        'subject' => $ticket->subject,
                        'message' => $ticket->message,
                        'priority' => $ticket->priority,
                        'status' => $ticket->status,
                        'createdAt' => $timestamp,
                    ]);

                    // 2. Initialize dedicated chat session for this ticket
                    $db->getReference("support_chats/{$ticketSessionId}/meta")->set([
                        'sessionId' => $ticketSessionId,
                        'isTicket' => true,
                        'ticketId' => $ticketId,
                        'ticketSubject' => $ticket->subject,
                        'ticketStatus' => 'open',
                        'category' => $ticket->category,
                        'priority' => $ticket->priority,
                        'userId' => $ticket->user_id,
                        'userName' => $ticket->name,
                        'userEmail' => $ticket->email,
                        'lastMessage' => "Ticket {$ticketId}: {$ticket->subject}",
                        'updatedAt' => $timestamp,
                        'status' => 'open',
                    ]);

                    // 3. Post initial ticket message into the ticket chat thread
                    $initMsgId = 'msg_tkt_init_' . Str::random(8);
                    $initMsgData = [
                        'id' => $initMsgId,
                        'sender' => 'user',
                        'senderName' => $ticket->name,
                        'senderEmail' => $ticket->email,
                        'userId' => $ticket->user_id,
                        'text' => "🎫 **Support Ticket Created: {$ticketId}**\n**Subject:** {$ticket->subject}\n**Category:** " . ucfirst($ticket->category) . " | **Priority:** " . ucfirst($ticket->priority) . "\n\n" . $ticket->message,
                        'timestamp' => $timestamp,
                        'status' => 'delivered',
                    ];
                    $db->getReference("support_chats/{$ticketSessionId}/messages/{$initMsgId}")->set($initMsgData);
                }
            } catch (\Throwable $e) {
                \Log::warning('Firebase support ticket sync failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'ticket_id' => $ticketId,
            'ticket_session_id' => $ticketSessionId,
            'ticket' => $ticket,
            'message' => 'Your ticket has been submitted successfully.',
        ]);
    }

    /**
     * Retrieve tickets for current session or user
     */
    public function getTickets(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'tickets' => [],
                'requires_auth' => true,
                'message' => 'Only authenticated users can access support tickets.'
            ], 401);
        }

        $sessionId = (string) $request->get('session_id', '');
        $user = Auth::user();

        $query = SupportTicket::query();

        $query->where(function ($q) use ($user, $sessionId) {
            $q->where('user_id', $user->id);
            if ($sessionId) {
                $q->orWhere('session_id', $sessionId);
            }
        });

        $tickets = $query->with('resolver:id,name')->orderBy('created_at', 'desc')->limit(20)->get();

        foreach ($tickets as $t) {
            if (!$t->session_id) {
                $t->session_id = 'sess_tkt_' . str_replace('-', '_', $t->ticket_id);
            }
        }

        return response()->json(['tickets' => $tickets]);
    }

    /**
     * Send a live chat message directly
     */
    public function sendChatMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'requires_auth' => true,
                'message' => 'Only authenticated users can send support messages.'
            ], 401);
        }

        $validated = $request->validate([
            'session_id' => 'required|string|max:128',
            'text' => 'required|string|max:3000',
            'sender_name' => 'nullable|string|max:100',
            'sender_email' => 'nullable|email|max:255',
        ]);

        $sessionId = $validated['session_id'];
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        $senderName = $isAdmin ? ($user->name ?? 'Support Agent') : $user->name;
        $senderEmail = $user->email;
        $timestamp = (int) (microtime(true) * 1000);
        $msgId = (string) ($request->input('message_id') ?: ('msg_' . Str::random(12)));

        // Block user messages if this session belongs to a closed/resolved ticket
        if (!$isAdmin) {
            $ticket = SupportTicket::where('session_id', $sessionId)->first();
            if ($ticket && in_array($ticket->status, ['resolved', 'closed'])) {
                return response()->json([
                    'success' => false,
                    'is_closed' => true,
                    'reopen_requested' => (bool) $ticket->reopen_requested,
                    'message' => 'This ticket is closed. You cannot send new messages, but you can request to reopen it.'
                ], 403);
            }
        }

        // Anti-duplicate protection for identical text in quick succession
        $cacheDuplicateKey = "recent_chat_msg_{$sessionId}_" . md5(trim($validated['text']));
        if ($request->input('message_id') && Cache::has($cacheDuplicateKey)) {
            return response()->json([
                'success' => true,
                'message' => 'Duplicate message ignored.',
                'message_id' => $msgId
            ]);
        }
        Cache::put($cacheDuplicateKey, true, now()->addSeconds(4));

        $sender = $isAdmin ? 'agent' : 'user';

        $messageData = [
            'id' => $msgId,
            'sender' => $sender,
            'senderName' => $senderName,
            'senderEmail' => $senderEmail,
            'userId' => $user?->id,
            'text' => trim($validated['text']),
            'timestamp' => $timestamp,
            'status' => 'sent',
        ];

        // Store message in Firebase RTDB
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $existing = $db->getReference("support_chats/{$sessionId}/messages/{$msgId}")->getValue();
                    if (!$existing) {
                        $db->getReference("support_chats/{$sessionId}/messages/{$msgId}")->set($messageData);
                    }

                    // Update chat thread session metadata
                    $db->getReference("support_chats/{$sessionId}/meta")->update([
                        'sessionId' => $sessionId,
                        'userId' => $user?->id,
                        'userName' => $senderName,
                        'userEmail' => $senderEmail,
                        'lastMessage' => Str::limit($messageData['text'], 100),
                        'updatedAt' => $timestamp,
                        'status' => 'active',
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firebase send chat message failed: ' . $e->getMessage());
            }
        }

        // Touch user presence cache (user is active in chat)
        \Illuminate\Support\Facades\Cache::put('support_chat_online_' . $sessionId, true, now()->addMinutes(3));
        if ($user) {
            \Illuminate\Support\Facades\Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(5));
        }

        // Check if all admins are offline, then send email & push alert
        try {
            $admins = \App\Models\User::where('is_admin', true)->get();
            $hasOnlineAdmin = \Illuminate\Support\Facades\Cache::has('admin_support_active') || 
                              $admins->contains(fn($a) => $a->isOnline());

            if (!$hasOnlineAdmin && $admins->isNotEmpty()) {
                $throttleKey = 'notified_admin_offline_chat_' . $sessionId;
                if (!\Illuminate\Support\Facades\Cache::has($throttleKey)) {
                    \Illuminate\Support\Facades\Cache::put($throttleKey, true, now()->addMinutes(10));

                    foreach ($admins as $admin) {
                        try {
                            $admin->notify(new \App\Notifications\NewSupportChatNotification($messageData, $sessionId));
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning('Failed sending offline admin chat email: ' . $e->getMessage());
                        }
                    }

                    // Save to admin_notifications in Firebase RTDB
                    if ($this->firebase->isConfigured()) {
                        try {
                            $db = $this->firebase->getDatabase();
                            if ($db) {
                                $notifId = uniqid('admin_chat_');
                                $db->getReference("admin_notifications/{$notifId}")->set([
                                    'id' => $notifId,
                                    'title' => 'New Live Support Chat',
                                    'message' => "{$senderName}: " . \Illuminate\Support\Str::limit($messageData['text'], 80),
                                    'type' => 'support_chat',
                                    'session_id' => $sessionId,
                                    'created_at' => (int) (microtime(true) * 1000),
                                    'read' => false,
                                ]);
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning('Admin notification DB write failed: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Admin offline notification dispatch failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $messageData,
        ]);
    }

    /**
     * Retrieve chat history for a session
     */
    public function getChatHistory(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'messages' => [],
                'meta' => null,
                'requires_auth' => true,
                'message' => 'Only authenticated users can view chat history.'
            ], 401);
        }

        $sessionId = (string) $request->get('session_id', '');

        if (!$sessionId) {
            return response()->json(['messages' => []]);
        }

        // Touch user presence cache (user is active)
        \Illuminate\Support\Facades\Cache::put('support_chat_online_' . $sessionId, true, now()->addMinutes(3));
        \Illuminate\Support\Facades\Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));

        $messages = [];
        $meta = null;

        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $raw = $db->getReference("support_chats/{$sessionId}/messages")->getValue();

                    if (is_array($raw)) {
                        $messages = array_values($raw);
                        usort($messages, fn($a, $b) => ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0));
                        $messages = array_slice($messages, -60);
                    }

                    $rawMeta = $db->getReference("support_chats/{$sessionId}/meta")->getValue();
                    if (is_array($rawMeta)) {
                        $meta = $rawMeta;
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firebase get chat history failed: ' . $e->getMessage());
            }
        }

        // If this is a ticket session, complement with MySQL ticket details
        if (str_starts_with($sessionId, 'sess_tkt_')) {
            $ticket = SupportTicket::where('session_id', $sessionId)->first();
            if ($ticket) {
                $meta = array_merge($meta ?? [], [
                    'isTicket' => true,
                    'ticketId' => $ticket->ticket_id,
                    'ticketSubject' => $ticket->subject,
                    'ticketStatus' => $ticket->status,
                    'reopenRequested' => (bool) $ticket->reopen_requested,
                    'reopenReason' => $ticket->reopen_reason,
                    'reopenDeclinedReason' => $ticket->reopen_declined_reason,
                    'resolutionNotes' => $ticket->resolution_notes,
                    'resolutionReason' => $ticket->resolution_reason,
                ]);
            }
        }

        return response()->json([
            'messages' => $messages,
            'meta' => $meta,
        ]);
    }

    /**
     * Request to reopen a resolved or closed support ticket
     */
    public function requestTicketReopen(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'requires_auth' => true,
                'message' => 'Only authenticated users can request to reopen tickets.'
            ], 401);
        }

        $validated = $request->validate([
            'ticket_id' => 'required_without:session_id|nullable|string|max:64',
            'session_id' => 'required_without:ticket_id|nullable|string|max:128',
            'reason' => 'required|string|min:5|max:1000',
        ], [
            'reason.required' => 'Please provide an explanation why you are requesting to reopen this ticket.',
            'reason.min' => 'Please provide at least 5 characters explaining your reason.',
        ]);

        $user = Auth::user();
        $ticketId = $validated['ticket_id'] ?? null;
        $sessionId = $validated['session_id'] ?? null;

        $query = SupportTicket::query();
        if ($ticketId) {
            $query->where('ticket_id', $ticketId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        $ticket = $query->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Support ticket not found.'
            ], 404);
        }

        // Authorization check: only ticket owner or session owner can request reopen
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        if (!$isAdmin) {
            if ($ticket->user_id && $ticket->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to reopen this ticket.'
                ], 403);
            }
        }

        if (!in_array($ticket->status, ['resolved', 'closed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is already open or in progress.',
                'ticket' => $ticket,
            ]);
        }

        if ($ticket->reopen_requested) {
            return response()->json([
                'success' => true,
                'reopen_requested' => true,
                'message' => 'A reopen request for this ticket is already pending review by support.',
                'ticket' => $ticket,
            ]);
        }

        $reasonText = trim($validated['reason']);
        $ticket->update([
            'reopen_requested' => true,
            'reopen_reason' => $reasonText,
            'reopen_requested_at' => now(),
            'reopen_declined_reason' => null,
            'reopen_declined_at' => null,
        ]);

        $userName = $user?->name ?: ($ticket->name ?: 'Requester');
        $timestamp = (int) (microtime(true) * 1000);

        // Sync to Firebase RTDB
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $db->getReference("support_tickets/{$ticket->ticket_id}")->update([
                        'reopenRequested' => true,
                        'reopenReason' => $reasonText,
                        'reopenRequestedAt' => now()->toIso8601String(),
                        'reopenRequestedBy' => $userName,
                        'reopenDeclinedReason' => null,
                        'reopenDeclinedAt' => null,
                    ]);

                    if ($ticket->session_id) {
                        $db->getReference("support_chats/{$ticket->session_id}/meta")->update([
                            'reopenRequested' => true,
                            'reopenReason' => $reasonText,
                            'reopenDeclinedReason' => null,
                            'reopenDeclinedAt' => null,
                            'lastMessage' => "Reopen requested: {$reasonText}",
                            'updatedAt' => $timestamp,
                        ]);

                        // Post system message in chat
                        $sysMsgId = 'sys_reopen_req_' . Str::random(10);
                        $sysText = "📩 **Reopen Request Submitted by {$userName}**\n**Reason:** {$reasonText}\n*Pending support agent review. Chat remains locked until approved.*";

                        $db->getReference("support_chats/{$ticket->session_id}/messages/{$sysMsgId}")->set([
                            'id' => $sysMsgId,
                            'sender' => 'system',
                            'senderName' => 'System',
                            'text' => $sysText,
                            'timestamp' => $timestamp,
                        ]);
                    }

                    // Alert admins in admin_notifications
                    $db->getReference('admin_notifications')->push([
                        'title' => 'Ticket Reopen Requested',
                        'message' => "Requester {$userName} requested to reopen ticket {$ticket->ticket_id}: {$reasonText}",
                        'type' => 'ticket_reopen_request',
                        'ticket_id' => $ticket->ticket_id,
                        'session_id' => $ticket->session_id,
                        'created_at' => $timestamp,
                        'read' => false,
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firebase ticket reopen request sync failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'reopen_requested' => true,
            'message' => 'Your reopen request has been submitted to support for review.',
            'ticket' => $ticket->fresh(['resolver']),
        ]);
    }
}
