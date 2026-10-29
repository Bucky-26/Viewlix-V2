@extends('layouts.admin')

@section('title', 'Live Support & Tickets')
@section('subtitle', 'Real-time customer live chat streams and support ticket management.')

@section('content')
<div x-data="adminSupportSystem()" class="space-y-4 w-full">

    {{-- Top Metrics & Controls Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full">
        {{-- Live Chats --}}
        <div @click="setTab('chat')" 
             class="p-3.5 rounded-xl bg-viewlix-dark border border-viewlix-red/30 hover:border-viewlix-red/60 flex items-center justify-between cursor-pointer transition-all">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-viewlix-red flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-viewlix-red"></span>
                    </span>
                    Live Chats
                </span>
                <div class="text-xl font-bold text-white mt-1" x-text="chatSessions.length || {{ $stats['active_chats'] }}">
                    {{ $stats['active_chats'] }}
                </div>
            </div>
            <div class="size-9 rounded-lg bg-viewlix-red/10 border border-viewlix-red/20 flex items-center justify-center text-viewlix-red">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>

        {{-- Open Tickets --}}
        <a href="{{ route('admin.support.index', ['status' => 'open', 'tab' => 'tickets']) }}" 
           class="p-3.5 rounded-xl bg-viewlix-dark border border-amber-500/20 hover:border-amber-500/50 flex items-center justify-between transition-all cursor-pointer group">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Open Tickets</span>
                <div class="text-xl font-bold text-white mt-1">{{ $stats['open'] }}</div>
            </div>
            <div class="size-9 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </a>

        {{-- In Progress --}}
        <a href="{{ route('admin.support.index', ['status' => 'in_progress', 'tab' => 'tickets']) }}" 
           class="p-3.5 rounded-xl bg-viewlix-dark border border-blue-500/20 hover:border-blue-500/50 flex items-center justify-between transition-all cursor-pointer group">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">In Progress</span>
                <div class="text-xl font-bold text-white mt-1">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="size-9 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
        </a>

        {{-- Resolved Tickets --}}
        <a href="{{ route('admin.support.index', ['status' => 'resolved', 'tab' => 'tickets']) }}" 
           class="p-3.5 rounded-xl bg-viewlix-dark border border-emerald-500/20 hover:border-emerald-500/50 flex items-center justify-between transition-all cursor-pointer group">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Resolved</span>
                <div class="text-xl font-bold text-white mt-1">{{ $stats['resolved'] }}</div>
            </div>
            <div class="size-9 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </a>
    </div>

    {{-- Tabs & Refresh Bar --}}
    <div class="flex items-center justify-between bg-viewlix-dark border border-viewlix-border p-2 rounded-2xl">
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="setTab('chat')"
                    :class="activeTab === 'chat' ? 'bg-viewlix-red text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-viewlix-gray'"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Live Chat Streams</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-black/40 font-mono" x-text="chatSessions.length"></span>
            </button>

            <button type="button"
                    @click="setTab('tickets')"
                    :class="activeTab === 'tickets' ? 'bg-viewlix-red text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-viewlix-gray'"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <span>Support Tickets</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-black/40 font-mono">{{ $stats['total'] }}</span>
            </button>
        </div>

        <button type="button" 
                @click="refreshData()"
                class="px-3 py-1.5 rounded-xl bg-viewlix-gray hover:bg-viewlix-gray-light border border-viewlix-border text-gray-300 hover:text-white text-xs flex items-center gap-1.5 transition-colors">
            <svg class="size-3.5" :class="{ 'animate-spin': isRefreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    {{-- TAB 1: LIVE CHAT CONSOLE --}}
    <div x-show="activeTab === 'chat'" x-cloak class="w-full flex flex-col lg:flex-row gap-4" style="height: 660px;">
        
        {{-- Left: Chat Sessions List --}}
        <div class="w-full lg:w-80 xl:w-96 shrink-0 bg-viewlix-dark border border-viewlix-border rounded-2xl flex flex-col overflow-hidden h-full shadow-lg">
            {{-- Sessions Header & Search --}}
            <div class="p-3 border-b border-viewlix-border space-y-2 bg-viewlix-darker/60">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-300 flex items-center gap-1.5">
                        <span>Conversations</span>
                        <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] font-mono text-gray-400" x-text="chatSessions.length"></span>
                    </h2>
                    <span class="text-[10px] text-emerald-400 flex items-center gap-1 font-medium">
                        <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Firebase Live
                    </span>
                </div>

                {{-- Session Type Filter Tabs: All / Live / Ticket --}}
                <div class="grid grid-cols-3 gap-1 p-1 bg-black/40 rounded-xl border border-viewlix-border/50 text-[11px] font-semibold">
                    <button type="button"
                            @click="sessionTypeFilter = 'all'"
                            :class="sessionTypeFilter === 'all' ? 'bg-viewlix-red text-white shadow-sm' : 'text-gray-400 hover:text-white'"
                            class="py-1 px-1 rounded-lg text-center transition-all flex items-center justify-center gap-1">
                        <span>All</span>
                        <span class="text-[9px] px-1 rounded-full bg-black/40 font-mono" x-text="chatSessions.length"></span>
                    </button>
                    <button type="button"
                            @click="sessionTypeFilter = 'live'"
                            :class="sessionTypeFilter === 'live' ? 'bg-viewlix-red text-white shadow-sm' : 'text-gray-400 hover:text-white'"
                            class="py-1 px-1 rounded-lg text-center transition-all flex items-center justify-center gap-1">
                        <span>💬 Live</span>
                        <span class="text-[9px] px-1 rounded-full bg-black/40 font-mono" x-text="liveChatCount"></span>
                    </button>
                    <button type="button"
                            @click="sessionTypeFilter = 'ticket'"
                            :class="sessionTypeFilter === 'ticket' ? 'bg-viewlix-red text-white shadow-sm' : 'text-gray-400 hover:text-white'"
                            class="py-1 px-1 rounded-lg text-center transition-all flex items-center justify-center gap-1">
                        <span>🎫 Tickets</span>
                        <span class="text-[9px] px-1 rounded-full bg-black/40 font-mono" x-text="ticketChatCount"></span>
                    </button>
                </div>

                <div class="relative">
                    <input type="text"
                           x-model="sessionFilter"
                           placeholder="Search users, ticket ID, message..."
                           class="w-full bg-viewlix-darker border border-viewlix-border rounded-xl text-xs px-3 py-1.5 pl-8 text-gray-200 placeholder-gray-500 focus:outline-none focus:border-viewlix-red">
                    <svg class="size-3.5 text-gray-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Sessions List Container --}}
            <div class="flex-1 overflow-y-auto divide-y divide-viewlix-border/40">
                <template x-for="sess in filteredSessions" :key="sess.session_id">
                    <button type="button"
                            @click="selectSession(sess)"
                            :class="selectedSession?.session_id === sess.session_id ? 'bg-viewlix-red/10 border-l-4 border-l-viewlix-red' : 'hover:bg-viewlix-gray/40'"
                            class="w-full text-left p-3 transition-colors flex items-start gap-3">
                        <div class="size-9 rounded-xl border flex items-center justify-center font-bold text-white text-xs shrink-0 mt-0.5"
                             :class="sess.is_ticket ? 'bg-gradient-to-tr from-amber-500/30 to-purple-600/30 border-amber-500/30' : 'bg-gradient-to-tr from-viewlix-red/30 to-blue-600/30 border-white/10'">
                            <span x-text="sess.is_ticket ? '🎫' : (sess.user_name || 'U').charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <p class="text-xs font-semibold text-white truncate" x-text="sess.user_name || 'Guest User'"></p>
                                <span class="text-[10px] text-gray-500 shrink-0" x-text="formatTime(sess.last_time)"></span>
                            </div>
                            <p class="text-[11px] text-gray-400 truncate mt-0.5" x-text="sess.last_message || 'Active conversation'"></p>
                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                <template x-if="sess.is_ticket">
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded border flex items-center gap-1"
                                          :class="sess.ticket_status === 'resolved' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : (sess.ticket_status === 'in_progress' ? 'bg-blue-500/15 text-blue-400 border-blue-500/30' : 'bg-amber-500/15 text-amber-400 border-amber-500/30')">
                                        <span>🎫</span>
                                        <span x-text="sess.ticket_id || 'Ticket'"></span>
                                        <span class="uppercase text-[8px] opacity-80" x-text="'(' + (sess.ticket_status || 'open') + ')'"></span>
                                    </span>
                                </template>
                                <template x-if="!sess.is_ticket">
                                    <span class="text-[9px] text-blue-300 bg-blue-500/10 border border-blue-500/20 px-1.5 py-0.5 rounded font-medium">💬 Live Chat</span>
                                </template>
                                <template x-if="sess.reopen_requested">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/40 flex items-center gap-1 animate-pulse shrink-0">
                                        <span>🔄</span> Reopen Req
                                    </span>
                                </template>
                                <span class="text-[9px] font-mono text-gray-500 bg-white/5 px-1.5 py-0.5 rounded truncate max-w-[100px]" x-text="sess.session_id"></span>
                                <span x-show="sess.status === 'agent_replied'" class="text-[9px] text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded font-medium shrink-0">Replied</span>
                            </div>
                        </div>
                    </button>
                </template>

                <div x-show="filteredSessions.length === 0" class="p-8 text-center text-gray-500 text-xs italic">
                    No conversations found under this filter.
                </div>
            </div>
        </div>

        {{-- Right: Live Conversation Viewer & Reply Box (Fills 100% Remaining Width) --}}
        <div class="flex-1 min-w-0 min-h-0 bg-viewlix-dark border border-viewlix-border rounded-2xl flex flex-col overflow-hidden shadow-lg">
            
            {{-- Empty State --}}
            <div x-show="!selectedSession" class="flex-1 flex flex-col items-center justify-center p-8 text-center text-gray-400 space-y-3">
                <div class="size-14 rounded-2xl bg-viewlix-gray/50 border border-viewlix-border flex items-center justify-center text-gray-500">
                    <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-white">Select a Conversation</h3>
                <p class="text-xs text-gray-400 max-w-sm">Choose an active user thread from the list on the left to review their live chat and reply in real-time.</p>
            </div>

            {{-- Selected Conversation --}}
            <div x-show="selectedSession" class="flex-1 min-h-0 flex flex-col overflow-hidden">
                <template x-if="selectedSession">
                    <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
                        {{-- Conversation Header --}}
                        <div class="p-3.5 border-b border-viewlix-border flex items-center justify-between bg-viewlix-darker/70 shrink-0 gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="size-9 rounded-xl border flex items-center justify-center font-bold text-white text-xs shrink-0"
                                     :class="selectedSession?.is_ticket ? 'bg-gradient-to-tr from-amber-500/30 to-purple-600/30 border-amber-500/30' : 'bg-gradient-to-tr from-viewlix-red/30 to-blue-600/30 border-white/10'">
                                    <span x-text="selectedSession?.is_ticket ? '🎫' : (selectedSession?.user_name || 'U').charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-xs font-bold text-white truncate" x-text="selectedSession?.user_name || 'Guest User'"></h3>
                                        <template x-if="selectedSession?.is_ticket">
                                            <span class="text-[10px] text-amber-300 bg-amber-500/15 border border-amber-500/30 px-1.5 py-0.5 rounded font-mono font-semibold">
                                                Ticket Chat
                                            </span>
                                        </template>
                                        <template x-if="!selectedSession?.is_ticket">
                                            <span class="text-[10px] text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20 font-medium shrink-0">Live Chat</span>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 mt-0.5 flex-wrap">
                                        <span x-show="selectedSession?.user_email" x-text="selectedSession?.user_email" class="text-gray-300 truncate"></span>
                                        <span class="font-mono text-gray-500 truncate" x-text="selectedSession?.session_id"></span>
                                        <span x-show="selectedSession?.ticket_subject" class="text-gray-300 truncate max-w-[200px]" x-text="'• ' + selectedSession?.ticket_subject"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                {{-- Ticket Inline Status Switcher if Ticket Chat --}}
                                <template x-if="selectedSession?.is_ticket && selectedSession?.ticket_id">
                                    <div class="flex items-center gap-1.5 bg-white/5 border border-white/10 rounded-xl p-1">
                                        <span class="text-[10px] text-gray-400 font-semibold uppercase px-1 hidden sm:inline">Status:</span>
                                        <select @change="updateTicketStatusInline($event.target.value)"
                                                :value="selectedSession?.ticket_status || 'open'"
                                                :disabled="isUpdatingStatus"
                                                class="bg-viewlix-darker border border-white/20 text-xs text-white rounded-lg px-2 py-1 focus:outline-none focus:border-viewlix-red cursor-pointer">
                                            <option value="open">Open 🟡</option>
                                            <option value="in_progress">In Progress ⏳</option>
                                            <option value="resolved">Resolved ✅</option>
                                        </select>
                                        <button type="button"
                                                @click="openTicketDetailModal(selectedSession.ticket)"
                                                class="text-[11px] px-2 py-1 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30 transition-colors font-medium">
                                            Details
                                        </button>
                                    </div>
                                </template>

                                <button type="button" 
                                        @click="copySessionId()"
                                        class="text-[11px] px-2.5 py-1.5 rounded-lg bg-viewlix-gray text-gray-300 hover:text-white border border-viewlix-border transition-colors">
                                    <span x-text="copied ? 'Copied!' : 'Copy ID'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Alert Banner: Pending Reopen Request --}}
                        <template x-if="selectedSession?.reopen_requested">
                            <div class="px-4 py-3 bg-gradient-to-r from-purple-950/70 via-purple-900/50 to-indigo-950/60 border-b border-purple-500/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shrink-0">
                                <div class="min-w-0 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/30 text-purple-200 border border-purple-400/40 animate-pulse">
                                            <span>🔄</span> User Requested Ticket Reopen
                                        </span>
                                        <span class="text-[10px] text-purple-300/70" x-show="selectedSession?.reopen_requested_at" x-text="formatDate(selectedSession.reopen_requested_at)"></span>
                                    </div>
                                    <div class="text-xs text-gray-200 break-words leading-relaxed">
                                        <span class="text-purple-300 font-semibold">User's Reason:</span>
                                        <span class="italic text-purple-100" x-text="'&quot;' + (selectedSession.reopen_reason || 'No specific reason provided') + '&quot;'"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button"
                                            @click="decideReopen(selectedSession.ticket || selectedSession.ticket_id, 'approve')"
                                            :disabled="isDecidingReopen"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-emerald-950/40">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Approve & Reopen</span>
                                    </button>
                                    <button type="button"
                                            @click="openDeclineModal(selectedSession.ticket || selectedSession.ticket_id, selectedSession.reopen_reason)"
                                            :disabled="isDecidingReopen"
                                            class="px-3 py-1.5 rounded-xl bg-red-600/20 hover:bg-red-600/40 text-red-300 hover:text-white border border-red-500/30 text-xs font-semibold transition-colors flex items-center gap-1">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>Decline...</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Messages Stream --}}
                        <div id="admin-chat-messages-container" class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-5 space-y-3.5 bg-[#0a0b12]">
                            <template x-for="msg in deduplicatedMessages" :key="msg.id">
                                <div class="w-full flex" :class="isAgent(msg) ? 'justify-end' : 'justify-start'">
                                    
                                    {{-- User Message (Left) --}}
                                    <div x-show="isUser(msg)" class="flex items-start gap-2.5 max-w-[85%] sm:max-w-[75%]">
                                        <div class="size-7 rounded-lg bg-viewlix-gray border border-white/10 flex items-center justify-center text-[10px] font-bold text-white shrink-0 mt-0.5">
                                            <span x-text="(msg.senderName || selectedSession?.user_name || 'U').charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div class="min-w-0 flex flex-col items-start">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <span class="text-[10px] font-semibold text-gray-300 truncate" x-text="msg.senderName || selectedSession?.user_name || 'User'"></span>
                                                <span class="text-[9px] text-gray-500 shrink-0" x-text="formatTime(msg.timestamp)"></span>
                                            </div>
                                            <div class="inline-block max-w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-2xl rounded-tl-sm bg-white/[0.08] border border-white/10 text-xs sm:text-[13px] text-gray-100 leading-relaxed shadow-sm break-words text-left" style="white-space: pre-wrap; word-break: break-word;" x-html="formatMessageText(msg.text)"></div>
                                        </div>
                                    </div>

                                    {{-- Support Agent Message (Right) --}}
                                    <div x-show="isAgent(msg)" class="flex flex-col items-end max-w-[85%] sm:max-w-[75%]">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="text-[9px] uppercase tracking-wider font-bold text-viewlix-red bg-viewlix-red/10 px-1.5 py-0.5 rounded border border-viewlix-red/30">Support Agent</span>
                                            <span class="text-[10px] font-semibold text-white" x-text="msg.senderName || 'Support Agent'"></span>
                                            <span class="text-[9px] text-gray-500" x-text="formatTime(msg.timestamp)"></span>
                                        </div>
                                        <div class="inline-block max-w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-2xl rounded-tr-sm bg-gradient-to-r from-viewlix-red to-blue-600 text-white text-xs sm:text-[13px] leading-relaxed shadow-md break-words text-left" style="white-space: pre-wrap; word-break: break-word;" x-html="formatMessageText(msg.text)"></div>
                                    </div>

                                    {{-- Bot / Automated Reply --}}
                                    <div x-show="isBot(msg)" class="flex items-start gap-2.5 max-w-[85%] sm:max-w-[75%]">
                                        <div class="size-7 rounded-lg bg-purple-900/40 border border-purple-500/30 flex items-center justify-center text-[11px] font-bold text-purple-300 shrink-0 mt-0.5">
                                            🤖
                                        </div>
                                        <div class="min-w-0 flex flex-col items-start">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <span class="text-[10px] font-semibold text-purple-300">Viewlix Bot</span>
                                                <span class="text-[9px] text-gray-500 shrink-0" x-text="formatTime(msg.timestamp)"></span>
                                            </div>
                                            <div class="inline-block max-w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-2xl rounded-tl-sm bg-purple-500/10 border border-purple-500/20 text-xs sm:text-[13px] text-purple-200 leading-relaxed shadow-sm break-words text-left" style="white-space: pre-wrap; word-break: break-word;" x-html="formatMessageText(msg.text)"></div>
                                        </div>
                                    </div>

                                    {{-- System Notification --}}
                                    <div x-show="isSystem(msg)" class="w-full flex justify-center my-1.5">
                                        <div class="inline-block px-3.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] text-gray-300 text-center max-w-[85%] break-words" style="white-space: pre-wrap; word-break: break-word;" x-html="formatMessageText(msg.text)"></div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="deduplicatedMessages.length === 0" class="py-16 text-center text-gray-500 text-xs italic">
                                No messages in this chat session yet.
                            </div>
                        </div>

                        {{-- Quick Response Chips --}}
                        <div class="px-3.5 py-2 bg-viewlix-dark border-t border-viewlix-border flex items-center gap-1.5 overflow-x-auto shrink-0">
                            <span class="text-[10px] text-gray-400 font-bold uppercase shrink-0 mr-1">Quick:</span>
                            <template x-for="template in cannedTemplates" :key="template">
                                <button type="button"
                                        @click="replyText = template"
                                        class="text-[11px] px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 hover:text-white whitespace-nowrap transition-colors"
                                        x-text="template">
                                </button>
                            </template>
                        </div>

                        {{-- Reply Input Box --}}
                        <form @submit.prevent="sendReply()" class="p-3 bg-viewlix-dark border-t border-viewlix-border flex items-center gap-2 shrink-0">
                            <div class="relative flex-1">
                                <input type="text"
                                       x-model="replyText"
                                       id="admin-reply-input"
                                       placeholder="Type reply to user... (Instant live delivery to their chat bubble)"
                                       class="w-full bg-viewlix-darker border border-viewlix-border rounded-xl text-xs px-3.5 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-viewlix-red">
                            </div>

                            <button type="submit"
                                    :disabled="!replyText.trim() || isSending"
                                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-red-600 hover:to-blue-700 disabled:opacity-50 text-white font-semibold text-xs flex items-center gap-1.5 transition-all shrink-0 shadow-md">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span x-text="isSending ? 'Sending...' : 'Send Reply'"></span>
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- TAB 2: SUPPORT TICKETS TABLE --}}
    <div x-show="activeTab === 'tickets'" x-cloak class="space-y-4 w-full">
        {{-- Filters & Search --}}
        <div class="bg-viewlix-dark border border-viewlix-border rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto">
                <a href="{{ route('admin.support.index', ['status' => 'all', 'tab' => 'tickets']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'all' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:text-white' }}">
                    All Tickets ({{ $stats['total'] }})
                </a>
                <a href="{{ route('admin.support.index', ['status' => 'open', 'tab' => 'tickets']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'open' ? 'bg-amber-500 text-black' : 'bg-viewlix-gray text-gray-300 hover:text-white' }}">
                    Open ({{ $stats['open'] }})
                </a>
                <a href="{{ route('admin.support.index', ['status' => 'in_progress', 'tab' => 'tickets']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'in_progress' ? 'bg-blue-500 text-white' : 'bg-viewlix-gray text-gray-300 hover:text-white' }}">
                    In Progress ({{ $stats['in_progress'] }})
                </a>
                <a href="{{ route('admin.support.index', ['status' => 'resolved', 'tab' => 'tickets']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'resolved' ? 'bg-emerald-500 text-white' : 'bg-viewlix-gray text-gray-300 hover:text-white' }}">
                    Resolved ({{ $stats['resolved'] }})
                </a>
                <a href="{{ route('admin.support.index', ['status' => 'reopen_requested', 'tab' => 'tickets']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $statusFilter === 'reopen_requested' ? 'bg-purple-600 text-white shadow-sm' : 'bg-viewlix-gray text-purple-300 hover:text-white' }} flex items-center gap-1.5">
                    <span>🔄 Reopen Requests</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-black/40 font-mono text-[10px]">{{ $stats['reopen_requested'] ?? 0 }}</span>
                </a>
            </div>

            <form method="GET" action="{{ route('admin.support.index') }}" class="flex items-center gap-2 w-full sm:w-72">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="hidden" name="tab" value="tickets">
                <div class="relative w-full">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search ticket ID, email, title..."
                           class="w-full bg-viewlix-darker border border-viewlix-border rounded-xl text-xs px-3 py-2 pl-8 text-gray-200 placeholder-gray-500 focus:outline-none focus:border-viewlix-red">
                    <svg class="size-4 text-gray-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>

        {{-- Tickets Table --}}
        <div class="bg-viewlix-dark border border-viewlix-border rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-viewlix-darker text-[11px] uppercase tracking-wider text-gray-400 border-b border-viewlix-border">
                        <tr>
                            <th class="p-3.5">Ticket ID</th>
                            <th class="p-3.5">Requester</th>
                            <th class="p-3.5">Subject & Message</th>
                            <th class="p-3.5">Category</th>
                            <th class="p-3.5">Priority</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Date</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-viewlix-border">
                        @forelse($tickets as $ticket)
                        <tr class="hover:bg-viewlix-gray/30 transition-colors">
                            {{-- Ticket ID --}}
                            <td class="p-3.5 font-mono font-bold text-white whitespace-nowrap">
                                <div>{{ $ticket->ticket_id }}</div>
                                @if($ticket->reopen_requested)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/40 animate-pulse">
                                            🔄 Reopen Requested
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Requester --}}
                            <td class="p-3.5">
                                <div class="font-medium text-white">{{ $ticket->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $ticket->email ?: 'No email provided' }}</div>
                                @if($ticket->session_id)
                                    <button type="button"
                                            @click="switchAndOpenChat('{{ $ticket->session_id }}', '{{ addslashes($ticket->name) }}')"
                                            class="text-[9px] text-blue-400 hover:underline flex items-center gap-1 mt-0.5">
                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>Open Live Chat</span>
                                    </button>
                                @endif
                            </td>

                            {{-- Subject & Message --}}
                            <td class="p-3.5 max-w-xs">
                                <div class="font-semibold text-white truncate">{{ $ticket->subject }}</div>
                                <div class="text-[11px] text-gray-400 line-clamp-2 mt-0.5">{{ $ticket->message }}</div>
                            </td>

                            {{-- Category --}}
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-[10px] uppercase font-semibold text-gray-300">
                                    {{ $ticket->category }}
                                </span>
                            </td>

                            {{-- Priority --}}
                            <td class="p-3.5 whitespace-nowrap">
                                @if($ticket->priority === 'urgent')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">URGENT</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">HIGH</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-500/20 text-gray-300 border border-gray-500/30">{{ strtoupper($ticket->priority) }}</span>
                                @endif
                            </td>

                            {{-- Status Dropdown --}}
                            <td class="p-3.5 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.support.ticket.status', $ticket) }}">
                                    @csrf
                                    <input type="hidden" name="tab" value="tickets">
                                    @if(request('status'))
                                        <input type="hidden" name="status_filter" value="{{ request('status') }}">
                                    @endif
                                    <select name="status" 
                                            @change="handleTableStatusChange($event, @js($ticket))"
                                            class="bg-viewlix-darker text-[11px] font-semibold rounded-lg px-2.5 py-1 border focus:outline-none 
                                            {{ $ticket->status === 'resolved' ? 'text-emerald-400 border-emerald-500/30' : ($ticket->status === 'in_progress' ? 'text-blue-400 border-blue-500/30' : 'text-amber-400 border-amber-500/30') }}">
                                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </form>
                            </td>

                            {{-- Date --}}
                            <td class="p-3.5 text-[11px] text-gray-400 whitespace-nowrap">
                                {{ $ticket->created_at->format('M d, Y') }}
                                <div class="text-[10px] text-gray-500">{{ $ticket->created_at->format('h:i A') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($ticket->reopen_requested)
                                        <button type="button"
                                                @click="openTicketDetailModal(@js($ticket))"
                                                class="px-2.5 py-1 rounded-lg bg-purple-600/30 hover:bg-purple-600 text-purple-200 hover:text-white border border-purple-500/40 text-[11px] font-semibold transition-colors flex items-center gap-1">
                                            <span>🔄 Review</span>
                                        </button>
                                    @endif
                                    <button type="button"
                                            @click="openTicketDetailModal(@js($ticket))"
                                            class="px-2.5 py-1 rounded-lg bg-viewlix-gray hover:bg-viewlix-red hover:text-white text-gray-300 text-[11px] font-medium transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto space-y-2.5">
                                    <div class="size-12 rounded-2xl bg-viewlix-gray/50 border border-viewlix-border flex items-center justify-center mx-auto text-gray-500">
                                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-300">
                                        @if($statusFilter !== 'all')
                                            No {{ str_replace('_', ' ', $statusFilter) }} tickets
                                        @elseif($search)
                                            No tickets matching "{{ $search }}"
                                        @else
                                            No support tickets found
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        @if($statusFilter !== 'all' || $search)
                                            There are no tickets under this filter.
                                            <a href="{{ route('admin.support.index', ['status' => 'all', 'tab' => 'tickets']) }}" class="text-viewlix-red hover:underline font-medium block mt-1">
                                                View all tickets ({{ $stats['total'] }})
                                            </a>
                                        @else
                                            Tickets submitted by users will appear here.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($tickets->hasPages())
            <div class="p-4 border-t border-viewlix-border">
                {{ $tickets->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Ticket Details Modal --}}
    <div x-show="ticketModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="ticketModalOpen = false" class="bg-viewlix-dark border border-viewlix-border rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-viewlix-border pb-3">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-sm font-bold text-white" x-text="activeTicket?.ticket_id"></span>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded border" 
                          :class="activeTicket?.status === 'resolved' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : (activeTicket?.status === 'in_progress' ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30')"
                          x-text="activeTicket?.status"></span>
                </div>
                <button type="button" @click="ticketModalOpen = false" class="text-gray-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px] uppercase">Subject</span>
                    <p class="text-sm font-semibold text-white mt-0.5" x-text="activeTicket?.subject"></p>
                </div>

                <div class="grid grid-cols-2 gap-3 p-2.5 rounded-xl bg-viewlix-darker">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase">Requester</span>
                        <span class="font-medium text-white" x-text="activeTicket?.name"></span>
                        <span class="text-gray-400 block text-[10px]" x-text="activeTicket?.email || 'No email'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase">Category / Priority</span>
                        <span class="text-gray-200 uppercase" x-text="activeTicket?.category"></span>
                        <span class="text-gray-400 block text-[10px]" x-text="'Priority: ' + activeTicket?.priority"></span>
                    </div>
                </div>

                <div>
                    <span class="text-gray-400 block text-[10px] uppercase">Message Body</span>
                    <div class="p-3 rounded-xl bg-viewlix-darker border border-viewlix-border text-gray-200 mt-1 whitespace-pre-wrap leading-relaxed max-h-48 overflow-y-auto" x-text="activeTicket?.message"></div>
                </div>

                {{-- Pending Reopen Request Card in Modal --}}
                <template x-if="activeTicket?.reopen_requested">
                    <div class="p-3.5 rounded-xl bg-gradient-to-br from-purple-950/60 to-indigo-950/40 border border-purple-500/40 space-y-2.5 shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-purple-300 flex items-center gap-1.5">
                                <span class="animate-pulse">🔄</span>
                                <span>Reopen Request from User</span>
                            </span>
                            <span class="text-[10px] text-purple-400 font-mono" x-show="activeTicket?.reopen_requested_at" x-text="formatDate(activeTicket?.reopen_requested_at)"></span>
                        </div>
                        <div class="text-xs text-gray-200">
                            <span class="text-[10px] uppercase text-purple-400/90 font-semibold block">Requester's Explanation:</span>
                            <div class="mt-1 p-2.5 rounded-lg bg-black/50 border border-purple-500/30 text-purple-100 whitespace-pre-wrap leading-relaxed italic" x-text="'&quot;' + (activeTicket?.reopen_reason || 'No explanation provided') + '&quot;'"></div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-purple-500/30">
                            <button type="button"
                                    @click="openDeclineModal(activeTicket, activeTicket?.reopen_reason)"
                                    :disabled="isDecidingReopen"
                                    class="px-3 py-1.5 rounded-lg bg-red-600/20 text-red-300 hover:bg-red-600/40 border border-red-500/30 text-xs font-medium transition-colors flex items-center gap-1">
                                <span>✕ Decline Request...</span>
                            </button>
                            <button type="button"
                                    @click="decideReopen(activeTicket, 'approve')"
                                    :disabled="isDecidingReopen"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-colors flex items-center gap-1 shadow-md shadow-emerald-900/30">
                                <span>✓ Approve & Reopen Ticket</span>
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Previous Reopen Request Declined Card --}}
                <template x-if="activeTicket?.reopen_declined_reason && !activeTicket?.reopen_requested">
                    <div class="p-3.5 rounded-xl bg-red-950/30 border border-red-500/30 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-red-300 flex items-center gap-1.5">
                                <span>✕ Previous Reopen Request Declined</span>
                            </span>
                            <span class="text-[10px] text-gray-400" x-show="activeTicket?.reopen_declined_at" x-text="'Declined: ' + formatDate(activeTicket?.reopen_declined_at)"></span>
                        </div>
                        <div class="text-xs text-gray-300">
                            <span class="text-[10px] uppercase text-red-400/80 font-semibold block">Decline Explanation / Message:</span>
                            <div class="mt-1 p-2.5 rounded-lg bg-black/40 border border-red-500/20 text-red-100 whitespace-pre-wrap leading-relaxed italic" x-text="'&quot;' + activeTicket?.reopen_declined_reason + '&quot;'"></div>
                        </div>
                    </div>
                </template>

                {{-- Resolution Details Card if Resolved --}}
                <template x-if="activeTicket?.status === 'resolved' || activeTicket?.resolution_notes">
                    <div class="p-3.5 rounded-xl bg-emerald-950/30 border border-emerald-500/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                                <span>✅ Resolution Summary</span>
                            </span>
                            <span class="text-[10px] text-gray-400" x-show="activeTicket?.resolved_at" x-text="'Resolved: ' + formatDate(activeTicket?.resolved_at)"></span>
                        </div>
                        <div class="text-xs text-gray-300">
                            <span class="text-[10px] uppercase text-emerald-400/80 font-semibold block">Action Taken:</span>
                            <span class="font-medium text-white" x-text="activeTicket?.resolution_reason || 'Issue Resolved'"></span>
                        </div>
                        <div class="text-xs text-gray-300">
                            <span class="text-[10px] uppercase text-emerald-400/80 font-semibold block">Resolution Notes:</span>
                            <div class="mt-1 p-2.5 rounded-lg bg-black/40 border border-emerald-500/20 text-emerald-100 whitespace-pre-wrap leading-relaxed" x-text="activeTicket?.resolution_notes"></div>
                        </div>
                        <template x-if="activeTicket?.resolver">
                            <div class="text-[10px] text-gray-400 pt-1 border-t border-emerald-500/20">
                                Resolved by: <span class="text-white font-medium" x-text="activeTicket.resolver.name"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-between border-t border-viewlix-border pt-3">
                <template x-if="activeTicket?.session_id">
                    <button type="button"
                            @click="switchAndOpenChat(activeTicket.session_id, activeTicket.name); ticketModalOpen = false;"
                            class="px-3 py-1.5 rounded-xl bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white border border-blue-500/30 text-xs font-semibold transition-colors flex items-center gap-1.5">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Open Live Chat Session</span>
                    </button>
                </template>
                <div class="flex items-center gap-2 ml-auto">
                    <template x-if="activeTicket?.status !== 'resolved'">
                        <button type="button"
                                @click="ticketModalOpen = false; openResolutionModal(activeTicket)"
                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Resolve Ticket...</span>
                        </button>
                    </template>
                    <button type="button" @click="ticketModalOpen = false" class="px-4 py-1.5 rounded-xl bg-viewlix-gray text-gray-300 hover:text-white text-xs font-semibold">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Ticket Resolution Modal --}}
    <div x-show="resolutionModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="if (!isSubmittingResolution) resolutionModalOpen = false" class="bg-viewlix-dark border border-viewlix-border rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-viewlix-border pb-3">
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-bold">✓</span>
                    <h3 class="font-bold text-sm text-white">Resolve Support Ticket</h3>
                    <span class="font-mono text-xs text-amber-400 font-semibold" x-text="resolvingTicket?.ticket_id"></span>
                </div>
                <button type="button" @click="resolutionModalOpen = false" :disabled="isSubmittingResolution" class="text-gray-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <div class="space-y-4 text-xs">
                {{-- Ticket Context Brief --}}
                <div class="p-3 rounded-xl bg-viewlix-darker border border-viewlix-border space-y-1">
                    <div class="flex items-center justify-between text-gray-400 text-[11px]">
                        <span>Requester: <strong class="text-gray-200" x-text="resolvingTicket?.name"></strong></span>
                        <span x-text="resolvingTicket?.email || 'No email'"></span>
                    </div>
                    <p class="font-semibold text-white truncate text-xs" x-text="resolvingTicket?.subject"></p>
                </div>

                {{-- Action Taken / Reason --}}
                <div>
                    <label class="block text-gray-300 font-medium mb-1.5">Action Taken / Resolution Category <span class="text-gray-500 font-normal">(Optional)</span></label>
                    <select x-model="resolutionForm.reason" class="w-full bg-viewlix-darker border border-viewlix-border rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="Issue Fixed / Player Working">Issue Fixed / Player Working</option>
                        <option value="Server / Stream Switched">Server / Stream Switched</option>
                        <option value="Movie / Episode Added or Updated">Movie / Episode Added or Updated</option>
                        <option value="Account / Profile Issue Resolved">Account / Profile Issue Resolved</option>
                        <option value="User Guided / Workaround Provided">User Guided / Workaround Provided</option>
                        <option value="Duplicate / Inactive Ticket">Duplicate / Inactive Ticket</option>
                        <option value="Other">Other Resolution</option>
                    </select>
                </div>

                {{-- Resolution Notes (Mandatory) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-gray-300 font-medium">Resolution Notes <span class="text-viewlix-red font-bold">*</span></label>
                        <span class="text-[10px]" :class="resolutionForm.notes.trim().length >= 5 ? 'text-emerald-400' : 'text-amber-400'" x-text="resolutionForm.notes.trim().length + '/5 min chars'"></span>
                    </div>
                    <textarea x-model="resolutionForm.notes" 
                              rows="4"
                              placeholder="Explain what was done to resolve this ticket (minimum 5 characters)..."
                              class="w-full bg-viewlix-darker border border-viewlix-border rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500"></textarea>
                    <template x-if="resolutionError">
                        <p class="text-[11px] text-red-400 mt-1 font-medium" x-text="resolutionError"></p>
                    </template>
                </div>

                {{-- Notify Requester Toggle --}}
                <div class="flex items-center gap-2.5 p-3 rounded-xl bg-viewlix-darker/70 border border-white/5">
                    <input type="checkbox" id="notify-user-check" x-model="resolutionForm.notifyUser" class="rounded bg-viewlix-dark border-viewlix-border text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 size-4">
                    <label for="notify-user-check" class="text-xs text-gray-300 select-none cursor-pointer">
                        <span class="font-semibold text-white block">Notify Requester</span>
                        <span class="text-[11px] text-gray-400">Drop resolution card into ticket chat thread & email requester if offline</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 border-t border-viewlix-border pt-3">
                <button type="button" 
                        @click="resolutionModalOpen = false" 
                        :disabled="isSubmittingResolution"
                        class="px-4 py-2 rounded-xl bg-viewlix-gray hover:bg-viewlix-gray-light text-gray-300 hover:text-white text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="button" 
                        @click="submitResolution()" 
                        :disabled="isSubmittingResolution || resolutionForm.notes.trim().length < 5"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 disabled:opacity-50 text-white font-semibold text-xs transition-all flex items-center gap-1.5 shadow-lg shadow-emerald-900/30">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isSubmittingResolution">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg class="size-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isSubmittingResolution">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="isSubmittingResolution ? 'Resolving...' : 'Confirm Resolution'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Decline Reopen Request Modal with Mandatory Explanation --}}
    <div x-show="declineModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="if (!isSubmittingDecline) declineModalOpen = false" class="bg-viewlix-dark border border-viewlix-border rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-viewlix-border pb-3">
                <div class="flex items-center gap-2">
                    <span class="size-6 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-xs font-bold">✕</span>
                    <h3 class="font-bold text-sm text-white">Decline Reopen Request</h3>
                    <span class="font-mono text-xs text-purple-400 font-semibold" x-text="declineTicket?.ticket_id || declineTicket"></span>
                </div>
                <button type="button" @click="declineModalOpen = false" :disabled="isSubmittingDecline" class="text-gray-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <div class="space-y-4 text-xs">
                {{-- User's Reopen Request Reason --}}
                <div class="p-3 rounded-xl bg-viewlix-darker border border-viewlix-border space-y-1">
                    <span class="text-gray-400 text-[10px] uppercase font-semibold block">User's Requested Reopen Reason:</span>
                    <p class="text-xs text-purple-200 italic leading-relaxed" x-text="'&quot;' + (declineUserReason || 'No explanation provided') + '&quot;'"></p>
                </div>

                {{-- Decline Explanation / Message (Mandatory) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-gray-300 font-medium">
                            Decline Explanation / Message for User <span class="text-viewlix-red font-bold">*</span>
                        </label>
                        <span class="text-[10px]" :class="declineNotes.trim().length >= 5 ? 'text-emerald-400' : 'text-amber-400'" x-text="declineNotes.trim().length + '/5 min chars'"></span>
                    </div>
                    <textarea x-model="declineNotes" 
                              rows="3"
                              placeholder="Explain to the user why this reopen request is being declined (minimum 5 characters)..."
                              class="w-full bg-viewlix-darker border border-viewlix-border rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-red-500 resize-none"></textarea>
                    <template x-if="declineError">
                        <p class="text-[11px] text-red-400 mt-1 font-medium" x-text="declineError"></p>
                    </template>
                </div>

                {{-- Quick Explanation Presets --}}
                <div class="space-y-1.5">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Quick Presets:</span>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" 
                                @click="declineNotes = 'The original issue was verified as resolved. Please submit a new ticket for new inquiries.'"
                                class="text-[10px] px-2 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 transition-colors">
                            Original Issue Verified
                        </button>
                        <button type="button" 
                                @click="declineNotes = 'Playback servers are operational. Please try switching servers or clearing browser cache.'"
                                class="text-[10px] px-2 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 transition-colors">
                            Servers Working / Cache
                        </button>
                        <button type="button" 
                                @click="declineNotes = 'Account verification and permissions have been confirmed. Please check your spam email folder.'"
                                class="text-[10px] px-2 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 transition-colors">
                            Account Confirmed
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 border-t border-viewlix-border pt-3">
                <button type="button" 
                        @click="declineModalOpen = false" 
                        :disabled="isSubmittingDecline"
                        class="px-4 py-2 rounded-xl bg-viewlix-gray hover:bg-viewlix-gray-light text-gray-300 hover:text-white text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="button" 
                        @click="submitDeclineReopen()" 
                        :disabled="isSubmittingDecline || declineNotes.trim().length < 5"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 disabled:opacity-50 text-white font-semibold text-xs transition-all flex items-center gap-1.5 shadow-lg shadow-red-950/40">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isSubmittingDecline">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <svg class="size-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isSubmittingDecline">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="isSubmittingDecline ? 'Declining...' : 'Confirm Decline with Message'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function adminSupportSystem() {
    return {
        activeTab: @js(request('tab', (request('status') || request('search')) ? 'tickets' : 'chat')),
        chatSessions: @js($chatSessions),
        sessionTypeFilter: 'all',
        sessionFilter: '',
        selectedSession: null,
        activeMessages: [],
        replyText: '',
        isSending: false,
        isRefreshing: false,
        isUpdatingStatus: false,
        copied: false,
        ticketModalOpen: false,
        activeTicket: null,
        sessionUnsubscribe: null,
        // Real-time updates handled entirely via Firebase RTDB WebSocket socket
        resolutionModalOpen: false,
        resolvingTicket: null,
        isSubmittingResolution: false,
        resolutionError: '',
        isDecidingReopen: false,
        declineModalOpen: false,
        declineTicket: null,
        declineUserReason: '',
        declineNotes: '',
        declineError: '',
        isSubmittingDecline: false,
        resolutionForm: {
            notes: '',
            reason: 'Issue Fixed / Player Working',
            notifyUser: true
        },

        cannedTemplates: [
            "👋 Hello! How can our support team assist you today?",
            "🔍 We are actively checking this playback issue right now.",
            "🎬 Your requested movie/show has been verified and added!",
            "✅ This issue is now resolved. Please refresh your player!",
            "⚙️ Try switching the video server from the dropdown above the player.",
            "🔒 For account verification issues, please check your spam folder."
        ],

        get liveChatCount() {
            return this.chatSessions.filter(s => !s.is_ticket).length;
        },

        get ticketChatCount() {
            return this.chatSessions.filter(s => !!s.is_ticket).length;
        },

        isAgent(msg) {
            if (!msg) return false;
            const s = (msg.sender || '').toLowerCase();
            return s === 'agent' || s === 'admin' || s.includes('viewlix') || !!msg.adminId;
        },

        isUser(msg) {
            if (!msg) return false;
            return (msg.sender || '').toLowerCase() === 'user';
        },

        isBot(msg) {
            if (!msg) return false;
            return (msg.sender || '').toLowerCase() === 'bot';
        },

        isSystem(msg) {
            if (!msg) return false;
            return (msg.sender || '').toLowerCase() === 'system';
        },

        formatMessageText(text) {
            if (!text) return '';
            const trimmed = String(text).trim();
            if (!trimmed) return '';
            let safe = trimmed
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            safe = safe.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer" class="underline text-blue-400 hover:text-blue-300">$1</a>');
            return safe;
        },

        setTab(tab) {
            this.activeTab = tab;
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url.toString());
            } catch (e) {}
        },

        get filteredSessions() {
            let list = this.chatSessions;
            if (this.sessionTypeFilter === 'live') {
                list = list.filter(s => !s.is_ticket);
            } else if (this.sessionTypeFilter === 'ticket') {
                list = list.filter(s => !!s.is_ticket);
            }
            if (!this.sessionFilter.trim()) return list;
            const q = this.sessionFilter.toLowerCase();
            return list.filter(s => 
                (s.user_name && s.user_name.toLowerCase().includes(q)) ||
                (s.user_email && s.user_email.toLowerCase().includes(q)) ||
                (s.session_id && s.session_id.toLowerCase().includes(q)) ||
                (s.ticket_id && s.ticket_id.toLowerCase().includes(q)) ||
                (s.ticket_subject && s.ticket_subject.toLowerCase().includes(q)) ||
                (s.last_message && s.last_message.toLowerCase().includes(q))
            );
        },

        get deduplicatedMessages() {
            const seen = new Set();
            return this.activeMessages.filter(msg => {
                if (!msg || !msg.text || !msg.text.trim()) return false;
                const timeBucket = Math.floor((msg.timestamp || 0) / 4000);
                const signature = `${msg.sender}_${msg.text.trim()}_${timeBucket}`;
                if (seen.has(msg.id) || seen.has(signature)) {
                    return false;
                }
                if (msg.id) seen.add(msg.id);
                seen.add(signature);
                return true;
            });
        },

        init() {
            // Check URL params or hash
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam === 'tickets' || tabParam === 'chat') {
                this.activeTab = tabParam;
            } else if (urlParams.has('status') || urlParams.has('search') || window.location.hash === '#tickets') {
                this.activeTab = 'tickets';
            }

            // Select first session if available and valid
            if (this.chatSessions.length > 0 && this.chatSessions[0]?.session_id) {
                this.selectSession(this.chatSessions[0], false);
            }

            // Real-time synchronization via Firebase RTDB WebSocket socket (Zero polling)
            this.initFirebaseLiveSync();
        },

        initFirebaseLiveSync() {
            const setup = () => {
                if (window.firebaseDb && window.FirebaseDbOps) {
                    try {
                        const { ref, onValue } = window.FirebaseDbOps;
                        const chatsRef = ref(window.firebaseDb, 'support_chats');
                        onValue(chatsRef, (snapshot) => {
                            const val = snapshot.val();
                            if (val && typeof val === 'object') {
                                Object.entries(val).forEach(([sid, chatData]) => {
                                    this.updateSessionInList(sid, chatData);
                                });
                            }
                        }, (err) => {
                            console.warn('RTDB support_chats error:', err);
                        });
                        console.log('⚡ Admin Live Support connected to RTDB WebSocket');

                        // If a session is already selected, attach its listener immediately
                        if (this.selectedSession && this.selectedSession.session_id) {
                            this.attachSessionListener(this.selectedSession.session_id);
                        }
                    } catch (e) {
                        console.warn('Admin Firebase sync notice:', e);
                    }
                }
            };

            if (window.firebaseDb && window.FirebaseDbOps) {
                setup();
            } else {
                const onReady = () => {
                    setup();
                    window.removeEventListener('firebase:ready', onReady);
                };
                window.addEventListener('firebase:ready', onReady);
                
                // Continuous retry fallback in case firebase:ready already fired
                let attempts = 0;
                const checkInterval = setInterval(() => {
                    attempts++;
                    if (window.firebaseDb && window.FirebaseDbOps) {
                        clearInterval(checkInterval);
                        setup();
                    } else if (attempts > 30) {
                        clearInterval(checkInterval);
                    }
                }, 200);
            }
        },

        updateSessionInList(sid, val) {
            const meta = val.meta || {};
            const messages = val.messages || {};
            const msgsList = Object.values(messages).sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));
            const lastMsg = msgsList.length > 0 ? msgsList[msgsList.length - 1] : null;

            const idx = this.chatSessions.findIndex(s => s.session_id === sid);
            const isTicket = !!(meta.isTicket || meta.ticketId || sid.startsWith('sess_tkt_') || (idx >= 0 && this.chatSessions[idx].is_ticket));
            const sessObj = {
                session_id: sid,
                user_name: meta.userName || (lastMsg ? lastMsg.senderName : 'Guest User'),
                user_email: meta.userEmail || (lastMsg ? lastMsg.senderEmail : ''),
                user_id: meta.userId || (lastMsg ? lastMsg.userId : null),
                status: meta.status || 'active',
                last_message: meta.lastMessage || (lastMsg ? lastMsg.text : ''),
                last_time: meta.updatedAt || (lastMsg ? lastMsg.timestamp : Date.now()),
                message_count: msgsList.length,
                is_ticket: isTicket,
                session_type: isTicket ? 'ticket' : 'live',
                ticket_id: meta.ticketId || (idx >= 0 ? this.chatSessions[idx].ticket_id : null),
                ticket_subject: meta.ticketSubject || (idx >= 0 ? this.chatSessions[idx].ticket_subject : null),
                ticket_status: meta.ticketStatus || (idx >= 0 ? this.chatSessions[idx].ticket_status : null),
                reopen_requested: (meta.reopenRequested !== undefined) ? !!meta.reopenRequested : (idx >= 0 ? !!this.chatSessions[idx].reopen_requested : false),
                reopen_reason: meta.reopenReason !== undefined ? meta.reopenReason : (idx >= 0 ? this.chatSessions[idx].reopen_reason : null),
                reopen_requested_at: meta.reopenRequestedAt !== undefined ? meta.reopenRequestedAt : (idx >= 0 ? this.chatSessions[idx].reopen_requested_at : null),
                reopen_declined_reason: (meta.reopenDeclinedReason !== undefined) ? meta.reopenDeclinedReason : (idx >= 0 ? this.chatSessions[idx].reopen_declined_reason : null),
                reopen_declined_at: (meta.reopenDeclinedAt !== undefined) ? meta.reopenDeclinedAt : (idx >= 0 ? this.chatSessions[idx].reopen_declined_at : null),
                ticket: (idx >= 0 && this.chatSessions[idx].ticket) ? this.chatSessions[idx].ticket : null,
            };

            if (idx >= 0) {
                this.chatSessions.splice(idx, 1, sessObj);
            } else {
                this.chatSessions.unshift(sessObj);
            }

            // Keep conversations sorted by latest activity descending and trigger Alpine reactivity
            this.chatSessions.sort((a, b) => (b.last_time || 0) - (a.last_time || 0));
            this.chatSessions = [...this.chatSessions];

            if (this.selectedSession && this.selectedSession.session_id === sid) {
                this.activeMessages = msgsList;
                this.selectedSession = { ...this.selectedSession, ...sessObj };
                this.scrollToBottom();
            }
        },

        async updateTicketStatusInline(newStatus) {
            if (!this.selectedSession || !this.selectedSession.ticket_id || this.isUpdatingStatus) return;

            const tktObj = this.selectedSession.ticket || {
                id: this.selectedSession.ticket_id,
                ticket_id: this.selectedSession.ticket_id,
                name: this.selectedSession.user_name,
                email: this.selectedSession.user_email,
                subject: this.selectedSession.ticket_subject,
                session_id: this.selectedSession.session_id,
                status: this.selectedSession.ticket_status
            };

            // Intercept 'resolved' to require resolution form
            if (newStatus === 'resolved') {
                this.openResolutionModal(tktObj);
                return;
            }

            this.isUpdatingStatus = true;
            const targetId = tktObj?.id || this.selectedSession.ticket_id;

            try {
                const res = await fetch(`/admin/support/ticket/${targetId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        notify_chat: true
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.selectedSession.ticket_status = newStatus;
                    if (this.selectedSession.ticket) {
                        this.selectedSession.ticket.status = newStatus;
                    }
                    const found = this.chatSessions.find(s => s.session_id === this.selectedSession.session_id);
                    if (found) {
                        found.ticket_status = newStatus;
                        if (found.ticket) found.ticket.status = newStatus;
                    }
                }
            } catch (err) {
                console.error('Error updating ticket status inline:', err);
            } finally {
                this.isUpdatingStatus = false;
            }
        },

        async selectSession(sess, shouldFocus = true) {
            if (!sess || !sess.session_id) {
                return;
            }
            this.selectedSession = sess;
            this.activeMessages = [];

            try {
                const res = await fetch(`/admin/support/chat/${encodeURIComponent(sess.session_id)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && Array.isArray(data.messages)) {
                        this.activeMessages = data.messages;
                        this.scrollToBottom();
                    }
                } else {
                    console.warn('Chat thread returned status:', res.status);
                }
            } catch (err) {
                console.warn('Failed to load thread via API:', err);
            }

            // Attach RTDB WebSocket listener to this conversation
            this.attachSessionListener(sess.session_id);

            if (shouldFocus) {
                setTimeout(() => {
                    const input = document.getElementById('admin-reply-input');
                    if (input) input.focus({ preventScroll: true });
                }, 100);
            }
        },

        attachSessionListener(sessionId) {
            if (!sessionId) return;

            // Unsubscribe previous session listener if any
            if (this.sessionUnsubscribe) {
                try { this.sessionUnsubscribe(); } catch(e) {}
                this.sessionUnsubscribe = null;
            }

            const hookListener = () => {
                if (window.firebaseDb && window.FirebaseDbOps && this.selectedSession && this.selectedSession.session_id === sessionId) {
                    try {
                        const { ref, onValue } = window.FirebaseDbOps;
                        const msgsRef = ref(window.firebaseDb, `support_chats/${sessionId}/messages`);
                        this.sessionUnsubscribe = onValue(msgsRef, (snapshot) => {
                            const val = snapshot.val();
                            if (val && typeof val === 'object' && this.selectedSession && this.selectedSession.session_id === sessionId) {
                                const list = Object.values(val).sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));
                                this.activeMessages = list;
                                this.scrollToBottom();
                            }
                        }, (err) => {
                            console.warn('RTDB messages listener notice:', err);
                        });
                    } catch (e) {
                        console.debug('Session listener notice:', e);
                    }
                }
            };

            if (window.firebaseDb && window.FirebaseDbOps) {
                hookListener();
            } else {
                window.addEventListener('firebase:ready', () => hookListener(), { once: true });
                let retries = 0;
                const interval = setInterval(() => {
                    retries++;
                    if (window.firebaseDb && window.FirebaseDbOps) {
                        clearInterval(interval);
                        hookListener();
                    } else if (retries > 30) {
                        clearInterval(interval);
                    }
                }, 200);
            }
        },

        async sendReply() {
            if (!this.replyText.trim() || !this.selectedSession || this.isSending) return;

            const textToSend = this.replyText.trim();
            const sid = this.selectedSession.session_id;
            this.isSending = true;

            const tempMsg = {
                id: 'agent_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36),
                sender: 'agent',
                senderName: 'Support Agent',
                text: textToSend,
                timestamp: Date.now(),
                status: 'delivered'
            };

            this.replyText = '';

            // Immediate optimistic UI render
            this.activeMessages.push(tempMsg);
            this.scrollToBottom();

            // 1. Direct RTDB write for instant sub-second delivery to user over WebSocket
            if (window.firebaseDb && window.FirebaseDbOps) {
                try {
                    const { ref, set, update } = window.FirebaseDbOps;
                    set(ref(window.firebaseDb, `support_chats/${sid}/messages/${tempMsg.id}`), tempMsg);
                    update(ref(window.firebaseDb, `support_chats/${sid}/meta`), {
                        lastMessage: textToSend.substring(0, 100),
                        lastAgentReply: textToSend,
                        updatedAt: tempMsg.timestamp,
                        status: 'agent_replied'
                    });
                } catch (e) {
                    console.warn('Direct RTDB write notice:', e);
                }
            }

            // 2. Dispatch to backend API for persistence and offline notification
            try {
                const res = await fetch('/admin/support/chat/reply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        session_id: sid,
                        text: textToSend,
                        message_id: tempMsg.id
                    })
                });

                const data = await res.json();
                if (data.success && data.message) {
                    const idx = this.activeMessages.findIndex(m => m.id === tempMsg.id);
                    if (idx >= 0) {
                        this.activeMessages[idx] = data.message;
                    }
                }
            } catch (err) {
                console.error('Failed to dispatch reply:', err);
                alert('Could not send reply. Please check your network connection.');
            } finally {
                this.isSending = false;
                this.scrollToBottom();
            }
        },

        switchAndOpenChat(sessionId, userName) {
            this.setTab('chat');
            let sess = this.chatSessions.find(s => s.session_id === sessionId);
            if (!sess) {
                sess = {
                    session_id: sessionId,
                    user_name: userName || 'Guest User',
                    user_email: '',
                    status: 'active',
                    last_message: 'Ticket inquiry',
                    last_time: Date.now()
                };
                this.chatSessions.unshift(sess);
            }
            this.selectSession(sess, true);
        },

        openTicketDetailModal(ticket) {
            this.activeTicket = ticket;
            this.ticketModalOpen = true;
        },

        openResolutionModal(ticket) {
            if (!ticket) return;
            this.resolvingTicket = ticket;
            this.resolutionError = '';
            this.resolutionForm.notes = '';
            this.resolutionForm.reason = 'Issue Fixed / Player Working';
            this.resolutionForm.notifyUser = true;
            this.resolutionModalOpen = true;
        },

        handleTableStatusChange(event, ticket) {
            const val = event.target.value;
            if (val === 'resolved') {
                event.target.value = ticket.status;
                this.openResolutionModal(ticket);
            } else {
                event.target.form.submit();
            }
        },

        async submitResolution() {
            if (!this.resolvingTicket) return;
            const notes = this.resolutionForm.notes.trim();
            if (notes.length < 5) {
                this.resolutionError = 'Resolution notes must be at least 5 characters long.';
                return;
            }

            this.isSubmittingResolution = true;
            this.resolutionError = '';
            const targetId = this.resolvingTicket.id || this.resolvingTicket.ticket_id;

            try {
                const res = await fetch(`/admin/support/ticket/${targetId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        status: 'resolved',
                        resolution_notes: notes,
                        resolution_reason: this.resolutionForm.reason,
                        notify_chat: true,
                        notify_user: this.resolutionForm.notifyUser
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.resolutionModalOpen = false;

                    // Update ticket in activeTicket if open
                    if (this.activeTicket && (this.activeTicket.id === targetId || this.activeTicket.ticket_id === targetId)) {
                        this.activeTicket.status = 'resolved';
                        this.activeTicket.resolution_notes = notes;
                        this.activeTicket.resolution_reason = this.resolutionForm.reason;
                        this.activeTicket.resolved_at = new Date().toISOString();
                    }

                    // Update in selectedSession if viewing this chat
                    if (this.selectedSession && (this.selectedSession.ticket_id === this.resolvingTicket.ticket_id || this.selectedSession.ticket?.id === targetId)) {
                        this.selectedSession.ticket_status = 'resolved';
                        if (this.selectedSession.ticket) {
                            this.selectedSession.ticket.status = 'resolved';
                            this.selectedSession.ticket.resolution_notes = notes;
                            this.selectedSession.ticket.resolution_reason = this.resolutionForm.reason;
                            this.selectedSession.ticket.resolved_at = new Date().toISOString();
                        }
                    }

                    // Instant RTDB WebSocket push
                    if (window.firebaseDb && window.FirebaseDbOps) {
                        try {
                            const { ref, update } = window.FirebaseDbOps;
                            const sid = this.selectedSession?.session_id || this.resolvingTicket?.session_id;
                            if (sid) {
                                update(ref(window.firebaseDb, `support_chats/${sid}/meta`), {
                                    ticketStatus: 'resolved',
                                    status: 'resolved',
                                    resolutionNotes: notes,
                                    resolutionReason: this.resolutionForm.reason,
                                    updatedAt: Date.now()
                                });
                            }
                            if (targetId) {
                                update(ref(window.firebaseDb, `support_tickets/${targetId}`), {
                                    status: 'resolved',
                                    resolutionNotes: notes,
                                    resolutionReason: this.resolutionForm.reason,
                                    resolvedAt: new Date().toISOString()
                                });
                            }
                        } catch (e) {}
                    }

                    // Also reload page if we are on tickets tab so the table reflects the database updates
                    if (this.activeTab === 'tickets') {
                        window.location.reload();
                    }
                } else {
                    this.resolutionError = data.message || 'Failed to resolve ticket. Please try again.';
                }
            } catch (err) {
                console.error('Resolution submission error:', err);
                this.resolutionError = 'An error occurred while resolving the ticket. Please try again.';
            } finally {
                this.isSubmittingResolution = false;
            }
        },

        async decideReopen(ticketOrId, decision) {
            if (!ticketOrId || this.isDecidingReopen) return;
            const targetId = typeof ticketOrId === 'object' ? (ticketOrId.id || ticketOrId.ticket_id) : ticketOrId;
            
            if (decision === 'decline') {
                this.openDeclineModal(ticketOrId, typeof ticketOrId === 'object' ? ticketOrId.reopen_reason : '');
                return;
            }

            if (!confirm('Are you sure you want to approve this reopen request and restore ticket status to Open?')) return;

            this.isDecidingReopen = true;
            try {
                const res = await fetch(`/admin/support/ticket/${targetId}/reopen-decision`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ decision })
                });

                const data = await res.json();
                if (data.success) {
                    if (this.selectedSession && (this.selectedSession.ticket_id === targetId || this.selectedSession.ticket?.id === targetId || this.selectedSession.ticket?.ticket_id === targetId)) {
                        this.selectedSession.ticket_status = 'open';
                        this.selectedSession.reopen_requested = false;
                        this.selectedSession.reopen_reason = null;
                        this.selectedSession.reopen_declined_reason = null;
                        if (this.selectedSession.ticket) {
                            this.selectedSession.ticket.status = 'open';
                            this.selectedSession.ticket.reopen_requested = false;
                            this.selectedSession.ticket.reopen_reason = null;
                            this.selectedSession.ticket.reopen_declined_reason = null;
                        }
                    }

                    if (this.activeTicket && (this.activeTicket.id === targetId || this.activeTicket.ticket_id === targetId)) {
                        this.activeTicket.status = 'open';
                        this.activeTicket.reopen_requested = false;
                        this.activeTicket.reopen_reason = null;
                        this.activeTicket.reopen_declined_reason = null;
                    }

                    const found = this.chatSessions.find(s => s.ticket_id === targetId || s.ticket?.id === targetId || s.session_id === targetId);
                    if (found) {
                        found.ticket_status = 'open';
                        found.reopen_requested = false;
                        found.reopen_reason = null;
                        found.reopen_declined_reason = null;
                        if (found.ticket) {
                            found.ticket.status = 'open';
                            found.ticket.reopen_requested = false;
                            found.ticket.reopen_reason = null;
                            found.ticket.reopen_declined_reason = null;
                        }
                    }

                    // Instant RTDB WebSocket push
                    if (window.firebaseDb && window.FirebaseDbOps) {
                        try {
                            const { ref, update } = window.FirebaseDbOps;
                            const sid = this.selectedSession?.session_id || found?.session_id;
                            if (sid) {
                                update(ref(window.firebaseDb, `support_chats/${sid}/meta`), {
                                    ticketStatus: 'open',
                                    status: 'active',
                                    reopenRequested: false,
                                    reopenDeclinedReason: null,
                                    reopenDeclinedAt: null,
                                    updatedAt: Date.now()
                                });
                            }
                            if (targetId) {
                                update(ref(window.firebaseDb, `support_tickets/${targetId}`), {
                                    status: 'open',
                                    reopenRequested: false,
                                    reopenDeclinedReason: null,
                                    reopenDeclinedAt: null
                                });
                            }
                        } catch (e) {}
                    }

                    if (this.activeTab === 'tickets') {
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'Failed to update reopen decision. Please try again.');
                }
            } catch (err) {
                console.error('Failed to decide reopen:', err);
                alert('Network error while processing reopen request.');
            } finally {
                this.isDecidingReopen = false;
            }
        },

        openDeclineModal(ticketOrId, userReason = '') {
            this.declineTicket = ticketOrId;
            this.declineUserReason = userReason || '';
            this.declineNotes = '';
            this.declineError = '';
            this.declineModalOpen = true;
        },

        async submitDeclineReopen() {
            if (!this.declineTicket || this.isSubmittingDecline) return;
            const targetId = typeof this.declineTicket === 'object' ? (this.declineTicket.id || this.declineTicket.ticket_id) : this.declineTicket;
            const notes = this.declineNotes.trim();
            if (notes.length < 5) {
                this.declineError = 'Please provide an explanation or message of at least 5 characters.';
                return;
            }

            this.isSubmittingDecline = true;
            this.declineError = '';

            try {
                const res = await fetch(`/admin/support/ticket/${targetId}/reopen-decision`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        decision: 'decline',
                        notes: notes
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.declineModalOpen = false;

                    if (this.selectedSession && (this.selectedSession.ticket_id === targetId || this.selectedSession.ticket?.id === targetId || this.selectedSession.ticket?.ticket_id === targetId)) {
                        this.selectedSession.reopen_requested = false;
                        this.selectedSession.reopen_declined_reason = notes;
                        if (this.selectedSession.ticket) {
                            this.selectedSession.ticket.reopen_requested = false;
                            this.selectedSession.ticket.reopen_declined_reason = notes;
                        }
                    }

                    if (this.activeTicket && (this.activeTicket.id === targetId || this.activeTicket.ticket_id === targetId)) {
                        this.activeTicket.reopen_requested = false;
                        this.activeTicket.reopen_declined_reason = notes;
                    }

                    const found = this.chatSessions.find(s => s.ticket_id === targetId || s.ticket?.id === targetId || s.session_id === targetId);
                    if (found) {
                        found.reopen_requested = false;
                        found.reopen_declined_reason = notes;
                        if (found.ticket) {
                            found.ticket.reopen_requested = false;
                            found.ticket.reopen_declined_reason = notes;
                        }
                    }

                    // Instant RTDB WebSocket push
                    if (window.firebaseDb && window.FirebaseDbOps) {
                        try {
                            const { ref, update } = window.FirebaseDbOps;
                            const sid = this.selectedSession?.session_id || found?.session_id;
                            if (sid) {
                                update(ref(window.firebaseDb, `support_chats/${sid}/meta`), {
                                    reopenRequested: false,
                                    reopenDeclinedReason: notes,
                                    reopenDeclinedAt: Date.now(),
                                    updatedAt: Date.now()
                                });
                            }
                            if (targetId) {
                                update(ref(window.firebaseDb, `support_tickets/${targetId}`), {
                                    reopenRequested: false,
                                    reopenDeclinedReason: notes,
                                    reopenDeclinedAt: new Date().toISOString()
                                });
                            }
                        } catch (e) {}
                    }

                    if (this.activeTab === 'tickets') {
                        window.location.reload();
                    }
                } else {
                    this.declineError = data.message || 'Failed to decline reopen request.';
                }
            } catch (err) {
                console.error('Failed to decline reopen request:', err);
                this.declineError = 'Network error while declining reopen request.';
            } finally {
                this.isSubmittingDecline = false;
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            try {
                const d = new Date(dateStr);
                return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                return dateStr;
            }
        },

        copySessionId() {
            if (!this.selectedSession) return;
            navigator.clipboard.writeText(this.selectedSession.session_id);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        formatTime(timestamp) {
            if (!timestamp) return 'Just now';
            const diff = Date.now() - timestamp;
            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            return new Date(timestamp).toLocaleDateString();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const c = document.getElementById('admin-chat-messages-container');
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        async refreshData() {
            this.isRefreshing = true;
            try {
                if (this.selectedSession) {
                    await this.selectSession(this.selectedSession, false);
                }
            } finally {
                setTimeout(() => this.isRefreshing = false, 600);
            }
        }
    };
}
</script>
@endpush
@endsection
