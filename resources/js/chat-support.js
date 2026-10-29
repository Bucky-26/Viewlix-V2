

import { getDatabase, ref, set, onValue, update } from 'firebase/database';

(function () {
    let db = null;
    let generalSessionId = null;
    let activeSessionId = null;
    let activeSessionType = 'live'; // 'live' | 'ticket'
    let activeTicketInfo = null; // { ticketId, subject, status }
    let sessionUnsubscribe = null;
    let isTicketView = false;
    const renderedMessageIds = new Set();
    let savedMessages = [];

    // Mobile & Touch Device Detection
    function isMobileOrTouch() {
        return window.matchMedia('(max-width: 768px)').matches || 
               'ontouchstart' in window || 
               navigator.maxTouchPoints > 0;
    }

    function isChatWindowOpen(chatWin) {
        if (!chatWin) return false;
        return !chatWin.classList.contains('pointer-events-none') && 
               !chatWin.classList.contains('opacity-0') &&
               chatWin.classList.contains('pointer-events-auto');
    }

    function focusInputSafely(el, chatWin) {
        if (!el || isMobileOrTouch()) return;
        if (!isChatWindowOpen(chatWin)) return;
        try {
            el.focus({ preventScroll: true });
        } catch (e) {}
    }

    function blurAllChatInputs(chatWin, ...elements) {
        elements.forEach(el => {
            try { if (el && typeof el.blur === 'function') el.blur(); } catch (e) {}
        });
        try {
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
                if (chatWin && chatWin.contains(activeEl)) {
                    activeEl.blur();
                }
            }
        } catch (e) {}
    }

    // Get or initialize persistent general live chat session ID
    function getGeneralSessionId(wrapEl = null) {
        const isAuth = wrapEl ? wrapEl.dataset.isAuthenticated === '1' : false;
        const uId = wrapEl?.dataset.userId;
        if (isAuth && uId) {
            return 'sess_user_' + uId;
        }
        let sid = localStorage.getItem('viewlix_general_session_id') || localStorage.getItem('viewlix_support_session_id');
        if (!sid) {
            sid = 'sess_gen_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
            localStorage.setItem('viewlix_general_session_id', sid);
        }
        return sid;
    }

    function formatTime(timestamp) {
        if (!timestamp) return 'Just now';
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function formatMessageText(text) {
        if (!text) return '';
        const trimmed = String(text).trim();
        if (!trimmed) return '';
        let safe = escapeHtml(trimmed);
        // Markdown bold **text**
        safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // URLs to safe clickable links
        safe = safe.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer" class="underline text-blue-400 hover:text-blue-300">$1</a>');
        return safe;
    }

    function isAgentSender(sender) {
        if (!sender) return false;
        const s = String(sender).toLowerCase();
        return s === 'agent' || s === 'admin' || s.includes('viewlix');
    }

    function saveToLocalCache(msg, sid) {
        if (!msg || !msg.id || !msg.text || !sid) return;
        const textKey = (msg.text || '').trim();
        const exists = savedMessages.some(m =>
            m.id === msg.id ||
            (m.sender === msg.sender && (m.text || '').trim() === textKey && Math.abs((m.timestamp || 0) - (msg.timestamp || 0)) < 5000)
        );
        if (!exists) {
            savedMessages.push(msg);
            savedMessages.sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));
            try {
                localStorage.setItem('viewlix_chat_history_' + sid, JSON.stringify(savedMessages.slice(-60)));
            } catch (e) { }
        }
    }

    function loadFromLocalCache(sid, appendFn, scrollFn) {
        try {
            const raw = localStorage.getItem('viewlix_chat_history_' + sid);
            if (raw) {
                const list = JSON.parse(raw);
                if (Array.isArray(list) && list.length > 0) {
                    const seen = new Set();
                    const cleanList = [];
                    list.forEach(msg => {
                        if (!msg || !msg.text) return;
                        const timeBucket = Math.floor((msg.timestamp || 0) / 4000);
                        const sig = `${msg.sender}_${msg.text.trim()}_${timeBucket}`;
                        if (!seen.has(msg.id) && !seen.has(sig)) {
                            if (msg.id) seen.add(msg.id);
                            seen.add(sig);
                            cleanList.push(msg);
                        }
                    });
                    savedMessages = cleanList;
                    cleanList.forEach(msg => appendFn(msg, false));
                    if (scrollFn) scrollFn();
                }
            }
        } catch (e) {
            console.warn('Local chat cache read error:', e);
        }
    }

    function initChatSupport() {
        const wrapper = document.getElementById('chat-support-wrapper');
        if (!wrapper) return;

        const isAuthenticated = wrapper.dataset.isAuthenticated === '1';
        const userId = wrapper.dataset.userId || null;
        generalSessionId = getGeneralSessionId(wrapper);
        activeSessionId = generalSessionId;

        const bubbleToggle = document.getElementById('chat-bubble-toggle');
        const iconOpen = document.getElementById('chat-bubble-icon-open');
        const iconClose = document.getElementById('chat-bubble-icon-close');
        const chatWindow = document.getElementById('chat-window');
        const windowClose = document.getElementById('chat-window-close');
        const teaserPill = document.getElementById('chat-teaser-pill');
        const teaserClose = document.getElementById('chat-teaser-close');

        const tabLive = document.getElementById('chat-tab-live');
        const tabTickets = document.getElementById('chat-tab-tickets');
        const btnNewChat = document.getElementById('chat-btn-new-chat');
        const ticketBanner = document.getElementById('chat-ticket-banner');
        const ticketBannerId = document.getElementById('chat-ticket-banner-id');
        const ticketBannerStatus = document.getElementById('chat-ticket-banner-status');
        const ticketBannerSubject = document.getElementById('chat-ticket-banner-subject');
        const ticketBackToLive = document.getElementById('chat-ticket-back-to-live');
        const sessionIndicator = document.getElementById('chat-session-indicator');

        const viewMessages = document.getElementById('chat-view-messages');
        const viewTicket = document.getElementById('chat-view-ticket');
        const ticketBackBtn = document.getElementById('ticket-back-to-chat');
        const openTicketLink = document.getElementById('chat-open-ticket-link');

        const activeInputContainer = document.getElementById('chat-active-input-container');
        const messageForm = document.getElementById('chat-message-form');
        const messageInput = document.getElementById('chat-message-input');
        const messagesContainer = document.getElementById('chat-messages-container');
        const defaultWelcome = document.getElementById('chat-default-welcome');
        const ticketForm = document.getElementById('support-ticket-form');
        const ticketHistoryContainer = document.getElementById('ticket-history-container');
        const refreshTicketsBtn = document.getElementById('refresh-tickets-btn');

        // Reopen request elements
        const closedFooter = document.getElementById('chat-ticket-closed-footer');
        const reopenStateClosed = document.getElementById('chat-reopen-state-closed');
        const reopenStatePending = document.getElementById('chat-reopen-state-pending');
        const reopenPendingReason = document.getElementById('chat-reopen-pending-reason');
        const closedTicketId = document.getElementById('chat-closed-ticket-id');
        const btnShowReopenForm = document.getElementById('chat-btn-show-reopen-form');
        const reopenFormContainer = document.getElementById('chat-reopen-form-container');
        const reopenReasonInput = document.getElementById('chat-reopen-reason-input');
        const btnCancelReopen = document.getElementById('chat-btn-cancel-reopen');
        const btnSubmitReopen = document.getElementById('chat-btn-submit-reopen');
        const ticketBannerReopenBtn = document.getElementById('chat-ticket-reopen-banner-btn');

        const userName = wrapper.dataset.userName || 'Guest User';
        const userEmail = wrapper.dataset.userEmail || '';

        // 1. Toggle Chat Window
        function toggleWindow(forceOpen = null) {
            const isOpen = forceOpen !== null ? !forceOpen : isChatWindowOpen(chatWindow);
            if (!isOpen) {
                // Open
                chatWindow.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
                chatWindow.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
                iconOpen.classList.add('hidden');
                iconClose.classList.remove('hidden');
                if (teaserPill) teaserPill.style.display = 'none';
                const unreadBadge = document.getElementById('chat-unread-badge');
                if (unreadBadge) unreadBadge.classList.add('hidden');

                // Only persist open state on desktop devices
                if (!isMobileOrTouch()) {
                    localStorage.setItem('viewlix_chat_open', '1');
                }

                if (!isTicketView) {
                    setTimeout(() => focusInputSafely(messageInput, chatWindow), 250);
                }
                scrollToBottom();
            } else {
                // Close & dismiss on-screen keyboard
                blurAllChatInputs(chatWindow, messageInput, reopenReasonInput);
                chatWindow.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                chatWindow.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
                localStorage.removeItem('viewlix_chat_open');
            }
        }

        bubbleToggle?.addEventListener('click', (e) => {
            e.preventDefault();
            toggleWindow();
        });

        windowClose?.addEventListener('click', (e) => {
            e.preventDefault();
            toggleWindow(false);
        });

        teaserClose?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (teaserPill) teaserPill.style.display = 'none';
        });

        // 2. View Switcher (Chat vs Ticket Form & List)
        function setView(toTicket) {
            isTicketView = toTicket;
            if (toTicket) {
                blurAllChatInputs(chatWindow, messageInput);
                viewMessages?.classList.add('hidden');
                viewTicket?.classList.remove('hidden');
                updateTabHighlight('tickets');
                loadTicketHistory();
            } else {
                viewTicket?.classList.add('hidden');
                viewMessages?.classList.remove('hidden');
                updateTabHighlight(activeSessionType === 'ticket' ? 'tickets' : 'live');
                if (isChatWindowOpen(chatWindow)) {
                    setTimeout(() => focusInputSafely(messageInput, chatWindow), 150);
                }
                scrollToBottom();
            }
        }

        function updateTabHighlight(activeTab) {
            if (activeTab === 'live') {
                tabLive?.classList.add('bg-viewlix-red', 'text-white', 'shadow-sm');
                tabLive?.classList.remove('bg-white/5', 'text-gray-300', 'border', 'border-white/10');
                tabTickets?.classList.remove('bg-viewlix-red', 'text-white', 'shadow-sm');
                tabTickets?.classList.add('bg-white/5', 'text-gray-300', 'border', 'border-white/10');
            } else {
                tabTickets?.classList.add('bg-viewlix-red', 'text-white', 'shadow-sm');
                tabTickets?.classList.remove('bg-white/5', 'text-gray-300', 'border', 'border-white/10');
                tabLive?.classList.remove('bg-viewlix-red', 'text-white', 'shadow-sm');
                tabLive?.classList.add('bg-white/5', 'text-gray-300', 'border', 'border-white/10');
            }
        }

        tabLive?.addEventListener('click', (e) => {
            e.preventDefault();
            if (activeSessionId !== generalSessionId || isTicketView) {
                switchSession(generalSessionId, 'live');
            }
            setView(false);
        });

        tabTickets?.addEventListener('click', (e) => {
            e.preventDefault();
            setView(true);
        });

        ticketBackBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            setView(false);
        });

        openTicketLink?.addEventListener('click', (e) => {
            e.preventDefault();
            setView(true);
        });

        ticketBackToLive?.addEventListener('click', (e) => {
            e.preventDefault();
            switchSession(generalSessionId, 'live');
        });

        btnNewChat?.addEventListener('click', (e) => {
            e.preventDefault();
            startNewGeneralChat();
        });

        // 3. Render Message in Chat Stream
        function appendMessage(msg, autoScroll = true) {
            if (!msg || !msg.id || !msg.text) return;

            // Deduplicate by message ID
            if (renderedMessageIds.has(msg.id)) return;

            // Deduplicate identical message text and sender within 4 seconds
            const timeKey = Math.floor((msg.timestamp || Date.now()) / 4000);
            const contentSig = `${msg.sender}_${(msg.text || '').trim()}_${timeKey}`;
            if (renderedMessageIds.has(contentSig)) return;

            renderedMessageIds.add(msg.id);
            renderedMessageIds.add(contentSig);

            // Hide default welcome once user or remote messages arrive
            if (defaultWelcome && activeSessionType === 'ticket') {
                defaultWelcome.style.display = 'none';
            }
            renderMessage(msg, autoScroll);
        }

        function renderMessage(msg, autoScroll = true) {
            if (!msg || !msg.id) return;
            const rawText = (msg.text || '').trim();
            if (!rawText) return;

            const isUser = msg.sender === 'user';
            const isAgent = isAgentSender(msg.sender);
            const isSystem = msg.sender === 'system';
            const isBot = msg.sender === 'bot';

            const msgEl = document.createElement('div');
            const timeStr = formatTime(msg.timestamp);
            const formattedText = formatMessageText(rawText);
            if (!formattedText) return;

            if (isUser) {
                msgEl.className = 'flex items-start gap-2.5 justify-end w-full';
                msgEl.innerHTML = `
                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col items-end">
                        <div class="inline-block max-w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-2xl rounded-tr-sm bg-gradient-to-r from-viewlix-red to-blue-600 text-white text-xs sm:text-[13px] leading-relaxed shadow-sm break-words text-left" style="white-space: pre-wrap; word-break: break-word;">${formattedText}</div>
                        <span class="text-[10px] text-gray-400 px-1 mt-1 text-right">${timeStr}</span>
                    </div>
                `;
            } else if (isSystem) {
                msgEl.className = 'w-full flex justify-center my-1.5';
                msgEl.innerHTML = `
                    <div class="inline-block px-3.5 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] text-gray-300 text-center max-w-[85%] break-words" style="white-space: pre-wrap; word-break: break-word;">${formattedText}</div>
                `;
            } else if (isBot) {
                msgEl.className = 'flex items-start gap-2.5 justify-start w-full';
                msgEl.innerHTML = `
                    <div class="w-7 h-7 rounded-lg bg-purple-900/40 border border-purple-500/30 flex items-center justify-center text-[11px] font-bold text-purple-300 flex-shrink-0 mt-0.5">
                        🤖
                    </div>
                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col items-start">
                        <div class="inline-block max-w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-2xl rounded-tl-sm bg-purple-500/10 border border-purple-500/20 text-xs sm:text-[13px] text-purple-200 leading-relaxed shadow-sm break-words text-left" style="white-space: pre-wrap; word-break: break-word;">${formattedText}</div>
                        <div class="flex items-center gap-1.5 px-1 mt-1">
                            <span class="text-[9px] text-gray-400">${timeStr}</span>
                            <span class="text-[9px] text-purple-400 font-semibold">• Bot</span>
                        </div>
                    </div>
                `;
            } else {
                // Support Agent Reply
                const agentName = msg.senderName || 'Support Agent';
                msgEl.className = 'flex items-start gap-2.5 justify-start w-full';
                msgEl.innerHTML = `
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-viewlix-red/40 to-blue-600/40 border border-white/10 flex items-center justify-center text-white flex-shrink-0 mt-0.5">
                        <img src="/logo.png" alt="V" class="w-4 h-4 object-contain">
                    </div>
                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col items-start">
                        <div class="inline-block max-w-full px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-2xl rounded-tl-sm bg-[#161722] border border-white/10 text-xs sm:text-[13px] text-gray-100 leading-relaxed shadow-sm break-words text-left" style="white-space: pre-wrap; word-break: break-word;">${formattedText}</div>
                        <div class="flex items-center gap-1.5 px-1 mt-1">
                            <span class="text-[9px] text-gray-400">${timeStr}</span>
                            <span class="text-[9px] text-blue-400 font-semibold">• ${escapeHtml(agentName)}</span>
                        </div>
                    </div>
                `;
            }

            if (messagesContainer) {
                messagesContainer.appendChild(msgEl);
                if (autoScroll) scrollToBottom();
            }
        }

        function scrollToBottom() {
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

        // 4. Multi-Session Switcher Engine
        function switchSession(targetSessionId, type = 'live', ticketInfo = null) {
            if (!targetSessionId) return;

            // Unsubscribe existing listener
            if (sessionUnsubscribe) {
                try { sessionUnsubscribe(); } catch (e) { }
                sessionUnsubscribe = null;
            }

            activeSessionId = targetSessionId;
            activeSessionType = type;
            activeTicketInfo = ticketInfo;
            savedMessages = [];
            renderedMessageIds.clear();

            // Clear visual messages container
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
            }

            // Update UI Banner and Indicators
            if (type === 'ticket' && ticketInfo) {
                if (ticketBanner) ticketBanner.classList.remove('hidden');
                if (ticketBannerId) ticketBannerId.textContent = ticketInfo.ticketId || 'Ticket';
                if (ticketBannerSubject) ticketBannerSubject.textContent = ticketInfo.subject || '';
                if (closedTicketId) closedTicketId.textContent = ticketInfo.ticketId || '';
                if (sessionIndicator) sessionIndicator.textContent = `🎫 Ticket: ${ticketInfo.ticketId || ''}`;
                updateTabHighlight('tickets');
                updateTicketChatLockState(ticketInfo.status, ticketInfo.reopen_requested, ticketInfo.reopen_reason, ticketInfo.reopen_declined_reason);
            } else {
                if (ticketBanner) ticketBanner.classList.add('hidden');
                if (sessionIndicator) sessionIndicator.textContent = '⚡ Live Chat Support';
                updateTabHighlight('live');
                updateTicketChatLockState('open', false, '');

                // Re-append default welcome in fresh live session
                if (defaultWelcome) {
                    messagesContainer?.appendChild(defaultWelcome);
                    defaultWelcome.style.display = 'flex';
                }
            }

            // Hydrate from local cache
            loadFromLocalCache(activeSessionId, appendMessage, scrollToBottom);

            // Hydrate remote history via API
            syncRemoteHistory(activeSessionId);

            // Attach Firebase listener to this session
            attachFirebaseListener(activeSessionId);

            setView(false);
            scrollToBottom();
        }

        // Start a fresh Live Chat conversation
        function startNewGeneralChat() {
            generalSessionId = 'sess_gen_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
            localStorage.setItem('viewlix_general_session_id', generalSessionId);
            switchSession(generalSessionId, 'live');

            const startNotice = {
                id: 'sys_new_' + Date.now(),
                sender: 'system',
                text: '✨ Started a fresh live support chat. How can we help you?',
                timestamp: Date.now()
            };
            appendMessage(startNotice, true);
            saveToLocalCache(startNotice, generalSessionId);
        }

        // 5. Initial Hydration: Sync Remote Chat History & Meta via API (One-time per session switch)
        async function syncRemoteHistory(sid) {
            if (!isAuthenticated || !sid) return;
            try {
                const res = await fetch(`/api/support/chat/history?session_id=${encodeURIComponent(sid)}`);
                if (!res.ok) return;
                const data = await res.json();
                if (data && sid === activeSessionId) {
                    if (Array.isArray(data.messages)) {
                        let hasNew = false;
                        data.messages.forEach(msg => {
                            if (!renderedMessageIds.has(msg.id)) {
                                hasNew = true;
                                appendMessage(msg, false);
                                saveToLocalCache(msg, sid);
                            }
                        });
                        if (hasNew) {
                            scrollToBottom();
                            const isOpen = chatWindow && chatWindow.classList.contains('pointer-events-auto');
                            if (!isOpen) {
                                const badge = document.getElementById('chat-unread-badge');
                                if (badge) badge.classList.remove('hidden');
                            }
                        }
                    }
                    if (data.meta && activeSessionType === 'ticket' && activeTicketInfo) {
                        if (data.meta.ticketStatus) {
                            activeTicketInfo.status = data.meta.ticketStatus;
                        }
                        if (data.meta.reopenRequested !== undefined) {
                            activeTicketInfo.reopen_requested = !!data.meta.reopenRequested;
                        }
                        if (data.meta.reopenReason !== undefined) {
                            activeTicketInfo.reopen_reason = data.meta.reopenReason;
                        }
                        if (data.meta.reopenDeclinedReason !== undefined) {
                            activeTicketInfo.reopen_declined_reason = data.meta.reopenDeclinedReason;
                        }
                        updateTicketChatLockState(
                            activeTicketInfo.status,
                            activeTicketInfo.reopen_requested,
                            activeTicketInfo.reopen_reason,
                            activeTicketInfo.reopen_declined_reason
                        );
                    }
                }
            } catch (err) {
                console.warn('API chat history sync notice:', err);
            }
        }

        // 6. Setup Firebase Realtime Database Listener (Live WebSocket Updates - Zero Polling)
        function attachFirebaseListener(sid) {
            if (!sid) return;

            try {
                const database = window.firebaseDb || (window.firebaseApp ? getDatabase(window.firebaseApp) : null);
                if (database) {
                    db = database;
                    const chatRef = ref(db, `support_chats/${sid}/messages`);

                    const unsubMessages = onValue(chatRef, (snapshot) => {
                        if (sid !== activeSessionId) return;
                        const data = snapshot.val();
                        if (data && typeof data === 'object') {
                            const sorted = Object.values(data).sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));
                            let hasNew = false;
                            sorted.forEach(msg => {
                                if (!renderedMessageIds.has(msg.id)) {
                                    hasNew = true;
                                    appendMessage(msg, true);
                                    saveToLocalCache(msg, sid);
                                }
                            });
                            if (hasNew) {
                                scrollToBottom();
                                const isOpen = chatWindow && chatWindow.classList.contains('pointer-events-auto');
                                if (!isOpen) {
                                    const badge = document.getElementById('chat-unread-badge');
                                    if (badge) badge.classList.remove('hidden');
                                }
                            }
                        }
                    }, (err) => {
                        console.warn('RTDB messages listener notice:', err);
                    });

                    // Also listen to chat meta for real-time ticket status & reopen updates
                    let unsubMeta = null;
                    try {
                        const metaRef = ref(db, `support_chats/${sid}/meta`);
                        unsubMeta = onValue(metaRef, (snapshot) => {
                            if (sid !== activeSessionId) return;
                            const meta = snapshot.val();
                            if (meta && typeof meta === 'object') {
                                if (activeSessionType === 'ticket' && activeTicketInfo) {
                                    if (meta.ticketStatus) {
                                        activeTicketInfo.status = meta.ticketStatus;
                                    }
                                    if (meta.reopenRequested !== undefined) {
                                        activeTicketInfo.reopen_requested = !!meta.reopenRequested;
                                    }
                                    if (meta.reopenReason !== undefined) {
                                        activeTicketInfo.reopen_reason = meta.reopenReason;
                                    }
                                    if (meta.reopenDeclinedReason !== undefined) {
                                        activeTicketInfo.reopen_declined_reason = meta.reopenDeclinedReason;
                                    }
                                    updateTicketChatLockState(
                                        activeTicketInfo.status,
                                        activeTicketInfo.reopen_requested,
                                        activeTicketInfo.reopen_reason,
                                        activeTicketInfo.reopen_declined_reason
                                    );
                                }
                            }
                        }, (err) => {
                            console.warn('RTDB meta listener notice:', err);
                        });
                    } catch (e) { }

                    sessionUnsubscribe = () => {
                        try { if (typeof unsubMessages === 'function') unsubMessages(); } catch (e) { }
                        try { if (typeof unsubMeta === 'function') unsubMeta(); } catch (e) { }
                    };

                    console.log('⚡ Connected to Firebase RTDB WebSocket session:', sid);
                }
            } catch (err) {
                console.warn('Firebase RTDB listener attach notice:', err);
            }
        }

        // Initialize session on startup
        switchSession(generalSessionId, 'live');

        // Attach Firebase as soon as ready
        if (window.firebaseDb || window.firebaseApp) {
            attachFirebaseListener(activeSessionId);
        }

        window.addEventListener('firebase:ready', () => {
            attachFirebaseListener(activeSessionId);
        });

        // Continuous retry check for Firebase readiness
        let fbCheckAttempts = 0;
        const fbCheckInterval = setInterval(() => {
            fbCheckAttempts++;
            if (window.firebaseDb || window.firebaseApp) {
                clearInterval(fbCheckInterval);
                attachFirebaseListener(activeSessionId);
            } else if (fbCheckAttempts > 30) {
                clearInterval(fbCheckInterval);
            }
        }, 200);

        window.addEventListener('load', () => {
            setTimeout(() => attachFirebaseListener(activeSessionId), 300);
        });

        // Restore chat window open state (desktop only, or explicit URL parameter)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('open_chat') === '1' || (!isMobileOrTouch() && localStorage.getItem('viewlix_chat_open') === '1')) {
            toggleWindow(true);
        }

        // 7. Send Chat Message
        async function sendMessage(text) {
            if (!text || !text.trim() || !activeSessionId) return;

            // Block guests from sending messages
            if (!isAuthenticated) {
                alert('Only authenticated users can send support messages. Please sign in to chat with support.');
                return;
            }

            // Block message sending if ticket is closed or resolved
            if (activeSessionType === 'ticket' && activeTicketInfo) {
                const isClosed = activeTicketInfo.status === 'resolved' || activeTicketInfo.status === 'closed';
                if (isClosed) {
                    alert('This ticket is closed. You cannot send new messages, but you can request to reopen it.');
                    return;
                }
            }

            const cleanText = text.trim();
            const sid = activeSessionId;

            const timestamp = Date.now();
            const msgId = 'msg_' + Math.random().toString(36).substring(2, 10) + timestamp.toString(36);

            const msgData = {
                id: msgId,
                sender: 'user',
                senderName: userName,
                senderEmail: userEmail,
                text: cleanText,
                timestamp: timestamp,
                status: 'sent'
            };

            // Immediate optimistic UI render
            appendMessage(msgData, true);
            saveToLocalCache(msgData, sid);

            // Write directly to Firebase RTDB for sub-second WebSocket delivery
            const database = db || window.firebaseDb || (window.firebaseApp ? getDatabase(window.firebaseApp) : null);
            if (database) {
                db = database;
                try {
                    const msgRef = ref(db, `support_chats/${sid}/messages/${msgId}`);
                    set(msgRef, msgData);

                    const metaRef = ref(db, `support_chats/${sid}/meta`);
                    const metaUpdate = {
                        sessionId: sid,
                        userId: wrapper.dataset.userId || null,
                        userName: userName,
                        userEmail: userEmail,
                        lastMessage: cleanText.substring(0, 100),
                        updatedAt: timestamp,
                        status: 'active'
                    };
                    if (activeSessionType === 'ticket' && activeTicketInfo) {
                        metaUpdate.isTicket = true;
                        metaUpdate.ticketId = activeTicketInfo.ticketId;
                        metaUpdate.ticketSubject = activeTicketInfo.subject;
                        metaUpdate.ticketStatus = activeTicketInfo.status;
                    }
                    update(metaRef, metaUpdate);
                } catch (e) {
                    console.warn('Direct RTDB write notice:', e);
                }
            }

            // Sync to backend API
            try {
                fetch('/api/support/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        session_id: sid,
                        text: cleanText,
                        sender_name: userName,
                        sender_email: userEmail,
                        message_id: msgId
                    })
                });
            } catch (err) {
                console.warn('Backend sync failed:', err);
            }
        }

        messageForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = messageInput?.value;
            if (text) {
                sendMessage(text);
                messageInput.value = '';
            }
        });

        // Quick prompt chips
        const chips = Array.from(document.querySelectorAll('.chat-chip'));
        chips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                e.preventDefault();
                const text = chip.textContent.trim().replace(/^[^\w]+/, '');
                if (text.toLowerCase().includes('ticket')) {
                    setView(true);
                } else {
                    if (!isAuthenticated) {
                        alert('Only authenticated users can send support messages. Please sign in to chat with support.');
                        return;
                    }
                    sendMessage(text);
                }
            });
        });

        // 7b. Ticket Chat Lock State & Reopen Request Handlers
        function updateTicketChatLockState(status, reopenRequested, reopenReason, reopenDeclinedReason) {
            const isClosed = status === 'resolved' || status === 'closed';

            if (ticketBannerStatus) {
                ticketBannerStatus.textContent = status || 'open';
                const statusClass = isClosed
                    ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                    : (status === 'in_progress' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'bg-blue-500/20 text-blue-400 border-blue-500/30');
                ticketBannerStatus.className = `text-[9px] uppercase font-bold px-1.5 py-0.2 rounded border ${statusClass}`;
            }

            const declinedNotice = document.getElementById('chat-ticket-declined-notice');
            const declinedReasonText = document.getElementById('chat-ticket-declined-reason-text');

            if (isClosed) {
                activeInputContainer?.classList.add('hidden');
                closedFooter?.classList.remove('hidden');

                if (reopenRequested) {
                    reopenStateClosed?.classList.add('hidden');
                    reopenStatePending?.classList.remove('hidden');
                    declinedNotice?.classList.add('hidden');
                    if (reopenPendingReason) {
                        reopenPendingReason.textContent = reopenReason ? `Reason: "${reopenReason}"` : '';
                    }
                    ticketBannerReopenBtn?.classList.add('hidden');
                } else {
                    reopenStateClosed?.classList.remove('hidden');
                    reopenStatePending?.classList.add('hidden');
                    reopenFormContainer?.classList.add('hidden');
                    if (btnShowReopenForm) btnShowReopenForm.classList.remove('hidden');
                    ticketBannerReopenBtn?.classList.remove('hidden');

                    if (reopenDeclinedReason) {
                        if (declinedReasonText) declinedReasonText.textContent = `Explanation: "${reopenDeclinedReason}"`;
                        declinedNotice?.classList.remove('hidden');
                    } else {
                        declinedNotice?.classList.add('hidden');
                    }
                }
            } else {
                activeInputContainer?.classList.remove('hidden');
                closedFooter?.classList.add('hidden');
                ticketBannerReopenBtn?.classList.add('hidden');
                declinedNotice?.classList.add('hidden');
            }
        }

        btnShowReopenForm?.addEventListener('click', (e) => {
            e.preventDefault();
            btnShowReopenForm.classList.add('hidden');
            reopenFormContainer?.classList.remove('hidden');
            focusInputSafely(reopenReasonInput, chatWindow);
        });

        ticketBannerReopenBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            btnShowReopenForm?.classList.add('hidden');
            reopenFormContainer?.classList.remove('hidden');
            focusInputSafely(reopenReasonInput, chatWindow);
        });

        btnCancelReopen?.addEventListener('click', (e) => {
            e.preventDefault();
            blurAllChatInputs(chatWindow, reopenReasonInput);
            reopenFormContainer?.classList.add('hidden');
            btnShowReopenForm?.classList.remove('hidden');
        });

        async function submitTicketReopenRequest() {
            if (!isAuthenticated) {
                alert('Only authenticated users can request to reopen tickets. Please sign in.');
                return;
            }
            if (!activeTicketInfo || !activeTicketInfo.ticketId) return;

            const reason = (reopenReasonInput?.value || '').trim();
            if (reason.length < 5) {
                alert('Please enter at least 5 characters explaining why you want to reopen this ticket.');
                focusInputSafely(reopenReasonInput, chatWindow);
                return;
            }

            if (btnSubmitReopen) {
                btnSubmitReopen.disabled = true;
                btnSubmitReopen.textContent = 'Submitting...';
            }

            try {
                const res = await fetch('/api/support/ticket/reopen-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        ticket_id: activeTicketInfo.ticketId,
                        session_id: activeSessionId,
                        reason: reason
                    })
                });

                const data = await res.json();
                if (data.success) {
                    activeTicketInfo.reopen_requested = true;
                    activeTicketInfo.reopen_reason = reason;
                    activeTicketInfo.reopen_declined_reason = null;
                    if (reopenReasonInput) reopenReasonInput.value = '';
                    updateTicketChatLockState(activeTicketInfo.status, true, reason, null);
                    loadTicketHistory();
                } else {
                    alert(data.message || 'Could not submit reopen request. Please try again.');
                }
            } catch (e) {
                console.error('Reopen request error:', e);
                alert('Network error submitting reopen request.');
            } finally {
                if (btnSubmitReopen) {
                    btnSubmitReopen.disabled = false;
                    btnSubmitReopen.textContent = 'Submit Reopen Request';
                }
            }
        }

        btnSubmitReopen?.addEventListener('click', (e) => {
            e.preventDefault();
            submitTicketReopenRequest();
        });

        // 8. Submit Support Ticket (Creates dedicated ticket thread)
        ticketForm?.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!isAuthenticated) {
                alert('Only authenticated users can request support. Please sign in.');
                return;
            }

            const submitBtn = document.getElementById('ticket-submit-btn');
            const subject = document.getElementById('ticket-subject')?.value;
            const category = document.getElementById('ticket-category')?.value;
            const priority = document.getElementById('ticket-priority')?.value;
            const email = document.getElementById('ticket-email')?.value;
            const message = document.getElementById('ticket-message')?.value;

            if (!subject || !message) return;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Submitting Ticket...</span>';
            }

            try {
                const response = await fetch('/api/support/ticket', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        session_id: generalSessionId,
                        subject,
                        category,
                        priority,
                        email,
                        name: userName,
                        message
                    })
                });

                const data = await response.json();

                if (data.success && data.ticket_session_id) {
                    ticketForm.reset();

                    // Switch immediately into dedicated ticket session chat thread!
                    switchSession(data.ticket_session_id, 'ticket', {
                        ticketId: data.ticket_id,
                        subject: subject,
                        status: 'open'
                    });
                } else {
                    alert('Error submitting ticket. Please try typing directly in the chat.');
                }
            } catch (err) {
                console.error('Ticket submit error:', err);
                alert('Network error. Please try again.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>Submit Support Ticket</span>';
                }
            }
        });

        // 9. Load Ticket History & Allow Opening Dedicated Ticket Chats
        async function loadTicketHistory() {
            if (!isAuthenticated || !ticketHistoryContainer) return;

            try {
                const res = await fetch(`/api/support/tickets?session_id=${encodeURIComponent(generalSessionId)}`);
                const data = await res.json();
                const tickets = data.tickets || [];

                if (tickets.length === 0) {
                    ticketHistoryContainer.innerHTML = '<p class="text-[11px] text-gray-400 italic">No tickets submitted yet.</p>';
                    return;
                }

                ticketHistoryContainer.innerHTML = '';
                tickets.forEach(t => {
                    const statusColor = t.status === 'resolved' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' :
                        (t.status === 'in_progress' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'bg-blue-500/20 text-blue-400 border-blue-500/30');

                    const item = document.createElement('div');
                    item.className = 'p-3 rounded-2xl bg-white/[0.04] border border-white/5 space-y-2 hover:border-white/10 transition-colors';
                    item.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-amber-300 font-bold">🎫 ${escapeHtml(t.ticket_id)}</span>
                            <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded border ${statusColor}">${escapeHtml(t.status)}</span>
                        </div>
                        <p class="text-xs font-semibold text-white truncate">${escapeHtml(t.subject)}</p>
                        <p class="text-[11px] text-gray-400 line-clamp-2 leading-relaxed">${escapeHtml(t.message)}</p>
                        ${t.reopen_requested ? `
                            <div class="mt-1.5 p-2 rounded-xl bg-amber-500/15 border border-amber-500/30 text-[11px] text-amber-200">
                                <div class="font-semibold text-amber-400 flex items-center gap-1">
                                    <span>⏳ Reopen Request Pending Review</span>
                                </div>
                                <p class="mt-0.5 text-amber-100/80 leading-normal line-clamp-2">${escapeHtml(t.reopen_reason || '')}</p>
                            </div>
                        ` : (t.reopen_declined_reason ? `
                            <div class="mt-1.5 p-2 rounded-xl bg-red-950/40 border border-red-500/30 text-[11px] text-red-200">
                                <div class="font-semibold text-red-400 flex items-center gap-1">
                                    <span>✕ Reopen Declined by Support:</span>
                                </div>
                                <p class="mt-0.5 text-gray-300 leading-normal line-clamp-2 italic">${escapeHtml(t.reopen_declined_reason)}</p>
                            </div>
                        ` : (t.status === 'resolved' && t.resolution_notes ? `
                            <div class="mt-1.5 p-2 rounded-xl bg-emerald-950/40 border border-emerald-500/20 text-[11px] text-emerald-200">
                                <div class="font-semibold text-emerald-400 flex items-center gap-1">
                                    <span>✅ Resolution (${escapeHtml(t.resolution_reason || 'Resolved')}):</span>
                                </div>
                                <p class="mt-0.5 text-gray-300 leading-normal line-clamp-2">${escapeHtml(t.resolution_notes)}</p>
                            </div>
                        ` : ''))}
                        <div class="pt-1 flex items-center justify-between border-t border-white/5 flex-wrap gap-1.5">
                            <span class="text-[10px] text-gray-500">${t.created_at ? new Date(t.created_at).toLocaleDateString() : ''}</span>
                            <div class="flex items-center gap-1.5">
                                ${((t.status === 'resolved' || t.status === 'closed') && !t.reopen_requested) ? `
                                    <button type="button" 
                                            class="ticket-quick-reopen-btn px-2.5 py-1 rounded-xl bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 text-[11px] font-semibold transition-all flex items-center gap-1"
                                            data-session-id="${escapeHtml(t.session_id)}"
                                            data-ticket-id="${escapeHtml(t.ticket_id)}"
                                            data-ticket-subject="${escapeHtml(t.subject)}"
                                            data-ticket-status="${escapeHtml(t.status)}">
                                        <span>🔄 Reopen</span>
                                    </button>
                                ` : ''}
                                <button type="button" 
                                        class="ticket-open-chat-btn px-2.5 py-1 rounded-xl bg-gradient-to-r from-blue-600/30 to-indigo-600/30 hover:from-blue-600 hover:to-indigo-600 text-blue-300 hover:text-white border border-blue-500/30 text-[11px] font-semibold transition-all flex items-center gap-1"
                                        data-session-id="${escapeHtml(t.session_id)}"
                                        data-ticket-id="${escapeHtml(t.ticket_id)}"
                                        data-ticket-subject="${escapeHtml(t.subject)}"
                                        data-ticket-status="${escapeHtml(t.status)}"
                                        data-reopen-requested="${t.reopen_requested ? '1' : '0'}"
                                        data-reopen-reason="${escapeHtml(t.reopen_reason || '')}"
                                        data-reopen-declined-reason="${escapeHtml(t.reopen_declined_reason || '')}"
                                        data-resolution-notes="${escapeHtml(t.resolution_notes || '')}"
                                        data-resolution-reason="${escapeHtml(t.resolution_reason || '')}">
                                    <span>💬 Chat on Ticket</span>
                                </button>
                            </div>
                        </div>
                    `;
                    ticketHistoryContainer.appendChild(item);
                });

                // Attach click handlers to open ticket chat buttons
                const chatBtns = ticketHistoryContainer.querySelectorAll('.ticket-open-chat-btn');
                chatBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const tSid = btn.getAttribute('data-session-id');
                        const tId = btn.getAttribute('data-ticket-id');
                        const tSub = btn.getAttribute('data-ticket-subject');
                        const tStat = btn.getAttribute('data-ticket-status');
                        const tReopenReq = btn.getAttribute('data-reopen-requested') === '1';
                        const tReopenReas = btn.getAttribute('data-reopen-reason');
                        const tReopenDecl = btn.getAttribute('data-reopen-declined-reason');
                        const tResNotes = btn.getAttribute('data-resolution-notes');
                        const tResReason = btn.getAttribute('data-resolution-reason');

                        if (tSid) {
                            switchSession(tSid, 'ticket', {
                                ticketId: tId,
                                subject: tSub,
                                status: tStat,
                                reopen_requested: tReopenReq,
                                reopen_reason: tReopenReas,
                                reopen_declined_reason: tReopenDecl,
                                resolutionNotes: tResNotes,
                                resolutionReason: tResReason
                            });
                        }
                    });
                });

                // Quick reopen buttons on ticket cards
                const reopenCardBtns = ticketHistoryContainer.querySelectorAll('.ticket-quick-reopen-btn');
                reopenCardBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const tSid = btn.getAttribute('data-session-id');
                        const tId = btn.getAttribute('data-ticket-id');
                        const tSub = btn.getAttribute('data-ticket-subject');
                        const tStat = btn.getAttribute('data-ticket-status');

                        if (tSid) {
                            switchSession(tSid, 'ticket', {
                                ticketId: tId,
                                subject: tSub,
                                status: tStat,
                                reopen_requested: false,
                                reopen_reason: ''
                            });
                            // Open the reopen form
                            btnShowReopenForm?.click();
                        }
                    });
                });
            } catch (err) {
                console.warn('Failed to fetch tickets:', err);
            }
        }

        refreshTicketsBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            loadTicketHistory();
        });

        // Close chat window & blur inputs when clicking outside
        document.addEventListener('click', (e) => {
            if (!chatWindow || !isChatWindowOpen(chatWindow)) return;
            if (chatWindow.contains(e.target) || bubbleToggle?.contains(e.target) || teaserPill?.contains(e.target)) return;
            toggleWindow(false);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChatSupport);
    } else {
        initChatSupport();
    }
})();
