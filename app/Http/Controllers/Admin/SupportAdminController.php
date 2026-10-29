<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportReplyNotification;
use App\Notifications\TicketResolvedNotification;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SupportAdminController extends Controller
{
    public function __construct(protected FirebaseService $firebase)
    {
    }

    /**
     * Admin Support Dashboard & Ticket Management
     */
    public function index(Request $request)
    {
        // Touch admin presence
        Cache::put('admin_support_active', true, now()->addMinutes(3));
        if (Auth::check()) {
            Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        }

        $statusFilter = $request->get('status', 'all');
        $search = $request->get('search', '');

        // Synchronize any tickets from Firebase RTDB to MySQL so all tickets show on admin side
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $rtdbTickets = $db->getReference('support_tickets')->getValue();
                    if (is_array($rtdbTickets)) {
                        foreach ($rtdbTickets as $tId => $tData) {
                            if (!is_array($tData)) {
                                continue;
                            }

                            $ticketId = !empty($tData['ticketId']) ? $tData['ticketId'] : $tId;
                            if (empty($ticketId)) {
                                continue;
                            }

                            $createdAt = !empty($tData['createdAt']) 
                                ? \Carbon\Carbon::createFromTimestampMs((int) $tData['createdAt']) 
                                : now();

                            SupportTicket::firstOrCreate(
                                ['ticket_id' => $ticketId],
                                [
                                    'session_id' => $tData['sessionId'] ?? ('sess_' . Str::random(12)),
                                    'user_id' => $tData['userId'] ?? null,
                                    'name' => $tData['userName'] ?? 'Guest User',
                                    'email' => $tData['userEmail'] ?? null,
                                    'category' => $tData['category'] ?? 'general',
                                    'subject' => $tData['subject'] ?? 'Support Inquiry',
                                    'message' => $tData['message'] ?? 'Ticket inquiry',
                                    'priority' => $tData['priority'] ?? 'normal',
                                    'status' => $tData['status'] ?? 'open',
                                    'created_at' => $createdAt,
                                    'updated_at' => $createdAt,
                                ]
                            );
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Sync RTDB tickets to MySQL notice: ' . $e->getMessage());
            }
        }

        // Query tickets from MySQL
        $ticketQuery = SupportTicket::with(['user', 'resolver']);

        if ($statusFilter === 'reopen_requested') {
            $ticketQuery->where('reopen_requested', true);
        } elseif ($statusFilter !== 'all' && in_array($statusFilter, ['open', 'in_progress', 'resolved', 'closed'])) {
            $ticketQuery->where('status', $statusFilter);
        }

        if ($search) {
            $ticketQuery->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('session_id', 'like', "%{$search}%");
            });
        }

        $tickets = $ticketQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ticket stats
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'reopen_requested' => SupportTicket::where('reopen_requested', true)->count(),
        ];

        // Retrieve active chat threads from Firebase RTDB
        $chatSessions = [];
        $existingSessionIds = [];

        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $rawChats = $db->getReference('support_chats')->getValue();
                    if (is_array($rawChats)) {
                        foreach ($rawChats as $sId => $chatData) {
                            $meta = $chatData['meta'] ?? [];
                            $messages = $chatData['messages'] ?? [];
                            $msgCount = is_array($messages) ? count($messages) : 0;
                            
                            // Find last message details
                            $lastMsgText = $meta['lastMessage'] ?? '';
                            $lastMsgTime = $meta['updatedAt'] ?? 0;

                            if (!$lastMsgText && is_array($messages) && !empty($messages)) {
                                $lastItem = end($messages);
                                $lastMsgText = $lastItem['text'] ?? '';
                                $lastMsgTime = $lastItem['timestamp'] ?? 0;
                            }

                            $isTicket = !empty($meta['isTicket']) || !empty($meta['ticketId']) || str_starts_with($sId, 'sess_tkt_');
                            $chatSessions[] = [
                                'session_id' => $sId,
                                'user_name' => $meta['userName'] ?? 'Guest User',
                                'user_email' => $meta['userEmail'] ?? '',
                                'user_id' => $meta['userId'] ?? null,
                                'status' => $meta['status'] ?? 'active',
                                'last_message' => $lastMsgText,
                                'last_time' => $lastMsgTime,
                                'message_count' => $msgCount,
                                'is_ticket' => (bool) $isTicket,
                                'session_type' => $isTicket ? 'ticket' : 'live',
                                'ticket_id' => $meta['ticketId'] ?? null,
                                'ticket_subject' => $meta['ticketSubject'] ?? null,
                                'ticket_status' => $meta['ticketStatus'] ?? null,
                                'reopen_requested' => (bool) ($meta['reopenRequested'] ?? false),
                                'reopen_reason' => $meta['reopenReason'] ?? null,
                                'reopen_requested_at' => $meta['reopenRequestedAt'] ?? null,
                                'reopen_declined_reason' => $meta['reopenDeclinedReason'] ?? null,
                                'reopen_declined_at' => $meta['reopenDeclinedAt'] ?? null,
                            ];
                            $existingSessionIds[] = $sId;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Admin fetch support_chats failed: ' . $e->getMessage());
            }
        }

        // Also add any tickets that have a session_id not yet in chatSessions
        $allTickets = SupportTicket::all();
        foreach ($allTickets as $t) {
            if ($t->session_id && !in_array($t->session_id, $existingSessionIds)) {
                $chatSessions[] = [
                    'session_id' => $t->session_id,
                    'user_name' => $t->name ?: 'Guest User',
                    'user_email' => $t->email ?: '',
                    'user_id' => $t->user_id,
                    'status' => $t->status ?: 'open',
                    'last_message' => "Ticket {$t->ticket_id}: {$t->subject}",
                    'last_time' => $t->created_at ? $t->created_at->timestamp * 1000 : 0,
                    'message_count' => 0,
                    'is_ticket' => true,
                    'session_type' => 'ticket',
                    'ticket_id' => $t->ticket_id,
                    'ticket_subject' => $t->subject,
                    'ticket_status' => $t->status,
                    'reopen_requested' => (bool) $t->reopen_requested,
                    'reopen_reason' => $t->reopen_reason,
                    'reopen_requested_at' => $t->reopen_requested_at ? $t->reopen_requested_at->toIso8601String() : null,
                    'reopen_declined_reason' => $t->reopen_declined_reason,
                    'reopen_declined_at' => $t->reopen_declined_at ? $t->reopen_declined_at->toIso8601String() : null,
                    'ticket' => $t,
                ];
                $existingSessionIds[] = $t->session_id;
            }
        }

        // Correlate tickets to each session
        $ticketLookup = $allTickets->groupBy('session_id');
        foreach ($chatSessions as &$sess) {
            if (isset($ticketLookup[$sess['session_id']])) {
                $firstTkt = $ticketLookup[$sess['session_id']]->first();
                $sess['ticket_id'] = $firstTkt->ticket_id;
                $sess['ticket_subject'] = $firstTkt->subject;
                $sess['ticket_status'] = $firstTkt->status;
                $sess['reopen_requested'] = (bool) ($firstTkt->reopen_requested ?: ($sess['reopen_requested'] ?? false));
                $sess['reopen_reason'] = $firstTkt->reopen_reason ?: ($sess['reopen_reason'] ?? null);
                $sess['reopen_requested_at'] = $firstTkt->reopen_requested_at ? $firstTkt->reopen_requested_at->toIso8601String() : ($sess['reopen_requested_at'] ?? null);
                $sess['reopen_declined_reason'] = $firstTkt->reopen_declined_reason ?: ($sess['reopen_declined_reason'] ?? null);
                $sess['reopen_declined_at'] = $firstTkt->reopen_declined_at ? $firstTkt->reopen_declined_at->toIso8601String() : ($sess['reopen_declined_at'] ?? null);
                $sess['ticket'] = $firstTkt;
                $sess['is_ticket'] = true;
                $sess['session_type'] = 'ticket';
            } elseif (str_starts_with($sess['session_id'], 'sess_tkt_')) {
                $sess['is_ticket'] = true;
                $sess['session_type'] = 'ticket';
            }
        }
        unset($sess);

        // Sort sessions by latest message timestamp descending
        usort($chatSessions, fn($a, $b) => ($b['last_time'] ?? 0) <=> ($a['last_time'] ?? 0));

        $stats['active_chats'] = count($chatSessions);

        return view('admin.support.index', compact('tickets', 'stats', 'chatSessions', 'statusFilter', 'search'));
    }

    /**
     * Polling endpoint for real-time live chat sessions and active conversation updates
     */
    public function pollChatUpdates(Request $request)
    {
        Cache::put('admin_support_active', true, now()->addMinutes(3));
        if (Auth::check()) {
            Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
        }

        $selectedSessionId = (string) ($request->input('session_id') ?: $request->input('selected_id') ?: '');
        $chatSessions = [];
        $activeMessages = [];

        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $rawChats = $db->getReference('support_chats')->getValue();
                    if (is_array($rawChats)) {
                        $allTickets = SupportTicket::all();
                        $ticketLookup = $allTickets->groupBy('session_id');

                        foreach ($rawChats as $sId => $chatData) {
                            $meta = $chatData['meta'] ?? [];
                            $messages = $chatData['messages'] ?? [];
                            $msgCount = is_array($messages) ? count($messages) : 0;

                            $lastMsgText = $meta['lastMessage'] ?? '';
                            $lastMsgTime = $meta['updatedAt'] ?? 0;

                            if (!$lastMsgText && is_array($messages) && !empty($messages)) {
                                $lastItem = end($messages);
                                $lastMsgText = $lastItem['text'] ?? '';
                                $lastMsgTime = $lastItem['timestamp'] ?? 0;
                            }

                            $isTicket = !empty($meta['isTicket']) || !empty($meta['ticketId']) || str_starts_with($sId, 'sess_tkt_') || isset($ticketLookup[$sId]);
                            $tkt = isset($ticketLookup[$sId]) ? $ticketLookup[$sId]->first() : null;

                            $chatSessions[] = [
                                'session_id' => $sId,
                                'user_name' => $meta['userName'] ?? ($tkt?->name ?: 'Guest User'),
                                'user_email' => $meta['userEmail'] ?? ($tkt?->email ?: ''),
                                'user_id' => $meta['userId'] ?? $tkt?->user_id,
                                'status' => $meta['status'] ?? 'active',
                                'last_message' => $lastMsgText,
                                'last_time' => $lastMsgTime,
                                'message_count' => $msgCount,
                                'is_ticket' => (bool) $isTicket,
                                'session_type' => $isTicket ? 'ticket' : 'live',
                                'ticket_id' => $meta['ticketId'] ?? $tkt?->ticket_id,
                                'ticket_subject' => $meta['ticketSubject'] ?? $tkt?->subject,
                                'ticket_status' => $meta['ticketStatus'] ?? $tkt?->status,
                                'reopen_requested' => (bool) ($tkt?->reopen_requested ?: ($meta['reopenRequested'] ?? false)),
                                'reopen_reason' => $tkt?->reopen_reason ?: ($meta['reopenReason'] ?? null),
                                'reopen_requested_at' => $tkt?->reopen_requested_at ? $tkt->reopen_requested_at->toIso8601String() : ($meta['reopenRequestedAt'] ?? null),
                                'reopen_declined_reason' => $tkt?->reopen_declined_reason ?: ($meta['reopenDeclinedReason'] ?? null),
                                'reopen_declined_at' => $tkt?->reopen_declined_at ? $tkt->reopen_declined_at->toIso8601String() : ($meta['reopenDeclinedAt'] ?? null),
                            ];
                        }

                        usort($chatSessions, fn($a, $b) => ($b['last_time'] ?? 0) <=> ($a['last_time'] ?? 0));
                    }

                    if ($selectedSessionId && isset($rawChats[$selectedSessionId]['messages']) && is_array($rawChats[$selectedSessionId]['messages'])) {
                        $activeMessages = array_values($rawChats[$selectedSessionId]['messages']);
                        usort($activeMessages, fn($a, $b) => ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Admin pollChatUpdates error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'chat_sessions' => $chatSessions,
            'active_messages' => $activeMessages,
        ]);
    }

    /**
     * Get real-time chat history for a session
     */
    public function getChatThread(Request $request, $sessionId = null)
    {
        if (empty($sessionId) || $sessionId === 'undefined') {
            return response()->json([
                'success' => true,
                'session_id' => '',
                'messages' => [],
            ]);
        }

        $messages = [];

        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $raw = $db->getReference("support_chats/{$sessionId}/messages")->getValue();
                    if (is_array($raw)) {
                        $messages = array_values($raw);
                        usort($messages, fn($a, $b) => ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0));
                    }
                }
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'messages' => $messages,
        ]);
    }

    /**
     * Send an admin reply to a user's chat session
     */
    public function sendChatReply(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string|max:128',
            'text' => 'required|string|max:3000',
        ]);

        $admin = Auth::user();
        $sessionId = $validated['session_id'];
        $timestamp = (int) (microtime(true) * 1000);
        $msgId = $request->input('message_id') ?: ('agent_' . Str::random(12));

        $replyData = [
            'id' => $msgId,
            'sender' => 'agent',
            'senderName' => $admin?->name ?? 'Support Agent',
            'senderEmail' => $admin?->email ?? 'support@viewlix.site',
            'adminId' => $admin?->id,
            'text' => trim($validated['text']),
            'timestamp' => $timestamp,
            'status' => 'delivered',
        ];

        // Store directly into Firebase RTDB under this session's message stream
        $sessionMeta = [];
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $db->getReference("support_chats/{$sessionId}/messages/{$msgId}")->set($replyData);

                    // Read current meta for user identification
                    $sessionMeta = $db->getReference("support_chats/{$sessionId}/meta")->getValue() ?? [];

                    // Update session meta
                    $db->getReference("support_chats/{$sessionId}/meta")->update([
                        'lastMessage' => Str::limit($replyData['text'], 100),
                        'lastAgentReply' => $replyData['text'],
                        'updatedAt' => $timestamp,
                        'status' => 'agent_replied',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Firebase admin reply failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'error' => 'Failed to send reply to Firebase.'], 500);
            }
        }

        // Check if recipient user is offline to send email & in-app notification
        try {
            $isUserChatOnline = Cache::has('support_chat_online_' . $sessionId);
            $recipientUser = null;
            $recipientEmail = null;
            $recipientName = 'Valued User';
            $firebaseUuid = null;

            if (!empty($sessionMeta['userId'])) {
                $recipientUser = User::find($sessionMeta['userId']);
                if ($recipientUser) {
                    $recipientEmail = $recipientUser->email;
                    $recipientName = $recipientUser->name;
                    $firebaseUuid = $recipientUser->firebase_uuid;
                }
            }

            if (!$recipientEmail && !empty($sessionMeta['userEmail'])) {
                $recipientEmail = $sessionMeta['userEmail'];
                $recipientName = $sessionMeta['userName'] ?? 'Valued User';
            }

            if (!$recipientEmail) {
                $tkt = SupportTicket::where('session_id', $sessionId)->whereNotNull('email')->first();
                if ($tkt) {
                    $recipientEmail = $tkt->email;
                    $recipientName = $tkt->name ?: $recipientName;
                    if (!$recipientUser && $tkt->user_id) {
                        $recipientUser = $tkt->user ?? User::find($tkt->user_id);
                    }
                }
            }

            if (!$recipientUser && $recipientEmail) {
                $recipientUser = User::where('email', $recipientEmail)->first();
            }

            if ($recipientUser) {
                $recipientEmail = $recipientUser->email;
                $recipientName = $recipientUser->name ?: $recipientName;
                $firebaseUuid = $recipientUser->firebase_uuid ?: $firebaseUuid;
            }

            $isUserSiteOnline = $recipientUser ? $recipientUser->isOnline() : false;
            $isUserOffline = !$isUserChatOnline && !$isUserSiteOnline;

            if ($isUserOffline) {
                // Send email notification (throttled 5 min per session)
                if ($recipientEmail) {
                    $throttleKey = 'notified_user_offline_reply_' . $sessionId;
                    if (!Cache::has($throttleKey)) {
                        Cache::put($throttleKey, true, now()->addMinutes(5));

                        try {
                            if ($recipientUser) {
                                $recipientUser->notify(new SupportReplyNotification($replyData, $recipientName));
                            } else {
                                Notification::route('mail', $recipientEmail)
                                    ->notify(new SupportReplyNotification($replyData, $recipientName));
                            }
                        } catch (\Throwable $e) {
                            Log::warning('Failed sending offline user support reply email: ' . $e->getMessage());
                        }
                    }
                }

                // In-app notification to user (if firebaseUuid is present)
                if ($firebaseUuid && isset($db) && $db) {
                    try {
                        $notifId = uniqid('chat_reply_');
                        $db->getReference("notifications/{$firebaseUuid}/{$notifId}")->set([
                            'id' => $notifId,
                            'title' => 'New Reply from Viewlix Support',
                            'message' => Str::limit($replyData['text'], 100),
                            'type' => 'support_chat',
                            'link' => '/?open_chat=1',
                            'created_at' => (int) (microtime(true) * 1000),
                            'sent_by' => $replyData['senderName'],
                            'read' => false,
                        ]);

                        // Send FCM push if token available
                        try {
                            $messaging = $this->firebase->getMessaging();
                            $tokensSnapshot = $db->getReference("fcmTokens/{$firebaseUuid}")->getSnapshot();
                            $tokensData = $tokensSnapshot->getValue() ?? [];
                            $tokens = [];
                            foreach ($tokensData as $tInfo) {
                                if (is_array($tInfo) && isset($tInfo['token'])) {
                                    $tokens[] = $tInfo['token'];
                                }
                            }
                            if (!empty($tokens) && $messaging) {
                                $fcmNotif = \Kreait\Firebase\Messaging\Notification::create(
                                    'New Support Reply',
                                    Str::limit($replyData['text'], 100)
                                );
                                foreach ($tokens as $tok) {
                                    try {
                                        $cMsg = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $tok)
                                            ->withNotification($fcmNotif)
                                            ->withData(['link' => '/?open_chat=1', 'type' => 'support_chat']);
                                        $messaging->send($cMsg);
                                    } catch (\Throwable $e) {}
                                }
                            }
                        } catch (\Throwable $e) {}
                    } catch (\Throwable $e) {
                        Log::warning('User in-app notification error: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('User offline notification check failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $replyData,
        ]);
    }

    /**
     * Update support ticket status and notify user in chat
     */
    public function updateTicketStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'notify_chat' => 'nullable|boolean',
            'notify_user' => 'nullable|boolean',
            'resolution_notes' => 'nullable|string|max:3000',
            'resolution_reason' => 'nullable|string|max:100',
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        // Enforce resolution notes when marking ticket as resolved or closed
        if ($newStatus === 'resolved' || $newStatus === 'closed') {
            $request->validate([
                'resolution_notes' => 'required|string|min:5|max:3000',
            ], [
                'resolution_notes.required' => 'Please provide resolution notes before marking the ticket as resolved.',
                'resolution_notes.min' => 'Resolution notes must be at least 5 characters long.',
            ]);
        }

        $ticketData = [
            'status' => $newStatus,
            'reopen_requested' => false,
        ];

        if ($newStatus === 'resolved' || $newStatus === 'closed') {
            $ticketData['resolution_notes'] = (string) $request->input('resolution_notes');
            $ticketData['resolution_reason'] = (string) ($request->input('resolution_reason') ?: 'Issue Resolved');
            $ticketData['resolved_at'] = now();
            $ticketData['resolved_by'] = Auth::id();
        }

        $ticket->update($ticketData);
        $agentName = Auth::user()?->name ?? 'Support Agent';

        // Sync status to Firebase RTDB
        if ($this->firebase->isConfigured()) {
            try {
                $db = $this->firebase->getDatabase();
                if ($db) {
                    $rtdbTicketData = [
                        'status' => $newStatus,
                    ];
                    if ($newStatus === 'resolved') {
                        $rtdbTicketData['resolutionNotes'] = $ticket->resolution_notes;
                        $rtdbTicketData['resolutionReason'] = $ticket->resolution_reason;
                        $rtdbTicketData['resolvedAt'] = $ticket->resolved_at?->toIso8601String();
                        $rtdbTicketData['resolvedBy'] = $agentName;
                    }

                    $db->getReference("support_tickets/{$ticket->ticket_id}")->update($rtdbTicketData);

                    if ($ticket->session_id) {
                        try {
                            $metaUpdate = [
                                'ticketStatus' => $newStatus,
                                'updatedAt' => (int) (microtime(true) * 1000),
                            ];
                            if ($newStatus === 'resolved') {
                                $metaUpdate['resolutionNotes'] = $ticket->resolution_notes;
                                $metaUpdate['resolutionReason'] = $ticket->resolution_reason;
                            }
                            $db->getReference("support_chats/{$ticket->session_id}/meta")->update($metaUpdate);
                        } catch (\Throwable $e) {}
                    }
                    
                    // Drop an automated update message into the user's ticket chat session
                    if ($ticket->session_id) {
                        $sysMsgId = 'sys_tkt_' . Str::random(10);
                        if ($newStatus === 'resolved') {
                            $reasonLine = $ticket->resolution_reason ? "**Action Taken:** {$ticket->resolution_reason}\n" : '';
                            $sysText = "✅ **Support Ticket Resolved: {$ticket->ticket_id}**\n{$reasonLine}**Resolution:** {$ticket->resolution_notes}\n*Resolved by {$agentName}*";
                        } else {
                            $statusLabels = [
                                'open' => 'Open 🟡',
                                'in_progress' => 'In Progress ⏳',
                            ];
                            $statusLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
                            $sysText = "Ticket update: Reference **{$ticket->ticket_id}** status has been updated to **{$statusLabel}** by our support team.";
                        }

                        $db->getReference("support_chats/{$ticket->session_id}/messages/{$sysMsgId}")->set([
                            'id' => $sysMsgId,
                            'sender' => 'system',
                            'senderName' => 'System',
                            'text' => $sysText,
                            'timestamp' => (int) (microtime(true) * 1000),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Firebase ticket status sync failed: ' . $e->getMessage());
            }
        }

        // Email notification if ticket was resolved and notification is requested
        if ($newStatus === 'resolved' && $request->boolean('notify_user', true)) {
            $recipientEmail = $ticket->email ?: $ticket->user?->email;
            if ($recipientEmail) {
                try {
                    if ($ticket->user) {
                        $ticket->user->notify(new TicketResolvedNotification($ticket, $agentName));
                    } else {
                        Notification::route('mail', $recipientEmail)
                            ->notify(new TicketResolvedNotification($ticket, $agentName));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed sending ticket resolved email: ' . $e->getMessage());
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'ticket' => $ticket->fresh(['resolver']),
                'message' => "Ticket {$ticket->ticket_id} status updated to {$newStatus}.",
            ]);
        }

        $redirectParams = ['tab' => 'tickets'];
        if ($request->filled('status_filter')) {
            $redirectParams['status'] = $request->input('status_filter');
        }

        return redirect()->route('admin.support.index', $redirectParams)
            ->with('success', "Ticket {$ticket->ticket_id} status updated to {$newStatus}.");
    }

    /**
     * Handle administrator decision on a ticket reopen request (approve / decline)
     */
    public function handleReopenDecision(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approve,decline',
            'notes' => 'required_if:decision,decline|nullable|string|min:5|max:1000',
        ], [
            'notes.required_if' => 'Please provide an explanation or message explaining why the reopen request is being declined.',
            'notes.min' => 'The decline explanation or message must be at least 5 characters long.',
        ]);

        $decision = $validated['decision'];
        $notes = trim((string) ($validated['notes'] ?? ''));
        $agentName = Auth::user()?->name ?? 'Support Agent';
        $timestamp = (int) (microtime(true) * 1000);

        if ($decision === 'approve') {
            $ticket->update([
                'status' => 'open',
                'reopen_requested' => false,
                'reopen_declined_reason' => null,
                'reopen_declined_at' => null,
            ]);

            if ($this->firebase->isConfigured()) {
                try {
                    $db = $this->firebase->getDatabase();
                    if ($db) {
                        $db->getReference("support_tickets/{$ticket->ticket_id}")->update([
                            'status' => 'open',
                            'reopenRequested' => false,
                            'reopenedAt' => now()->toIso8601String(),
                            'reopenedBy' => $agentName,
                            'reopenDeclinedReason' => null,
                            'reopenDeclinedAt' => null,
                        ]);

                        if ($ticket->session_id) {
                            $db->getReference("support_chats/{$ticket->session_id}/meta")->update([
                                'ticketStatus' => 'open',
                                'status' => 'active',
                                'reopenRequested' => false,
                                'reopenDeclinedReason' => null,
                                'reopenDeclinedAt' => null,
                                'lastMessage' => 'Reopen request approved by support',
                                'updatedAt' => $timestamp,
                            ]);

                            $sysMsgId = 'sys_reopen_appr_' . Str::random(10);
                            $sysText = "✅ **Reopen Request Approved by Support ({$agentName})**\n";
                            if ($notes) {
                                $sysText .= "**Note:** {$notes}\n";
                            }
                            $sysText .= "*Ticket status has been restored to Open. You may now continue messaging.*";

                            $db->getReference("support_chats/{$ticket->session_id}/messages/{$sysMsgId}")->set([
                                'id' => $sysMsgId,
                                'sender' => 'system',
                                'senderName' => 'System',
                                'text' => $sysText,
                                'timestamp' => $timestamp,
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Firebase approve reopen error: ' . $e->getMessage());
                }
            }

            $message = "Ticket {$ticket->ticket_id} reopen request approved and status restored to open.";
        } else {
            // Decline with mandatory explanation
            $ticket->update([
                'reopen_requested' => false,
                'reopen_declined_reason' => $notes,
                'reopen_declined_at' => now(),
            ]);

            if ($this->firebase->isConfigured()) {
                try {
                    $db = $this->firebase->getDatabase();
                    if ($db) {
                        $db->getReference("support_tickets/{$ticket->ticket_id}")->update([
                            'reopenRequested' => false,
                            'reopenDeclinedReason' => $notes,
                            'reopenDeclinedAt' => now()->toIso8601String(),
                            'reopenDeclinedBy' => $agentName,
                        ]);

                        if ($ticket->session_id) {
                            $db->getReference("support_chats/{$ticket->session_id}/meta")->update([
                                'reopenRequested' => false,
                                'reopenDeclinedReason' => $notes,
                                'reopenDeclinedAt' => $timestamp,
                                'lastMessage' => 'Reopen request declined: ' . Str::limit($notes, 80),
                                'updatedAt' => $timestamp,
                            ]);

                            $sysMsgId = 'sys_reopen_decl_' . Str::random(10);
                            $sysText = "❌ **Reopen Request Declined by Support ({$agentName})**\n\n"
                                     . "**Explanation / Message:**\n{$notes}\n\n"
                                     . "*If you are experiencing a new or different issue, please submit a new support ticket.*";

                            $db->getReference("support_chats/{$ticket->session_id}/messages/{$sysMsgId}")->set([
                                'id' => $sysMsgId,
                                'sender' => 'system',
                                'senderName' => 'System',
                                'text' => $sysText,
                                'timestamp' => $timestamp,
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Firebase decline reopen error: ' . $e->getMessage());
                }
            }

            $message = "Ticket {$ticket->ticket_id} reopen request declined with explanation.";
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'decision' => $decision,
                'ticket' => $ticket->fresh(['resolver']),
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.support.index', ['tab' => 'tickets'])->with('success', $message);
    }
}
