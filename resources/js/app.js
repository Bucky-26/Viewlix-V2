import './bootstrap';

import Alpine from 'alpinejs';

// Preline UI - Import all components
import { HSDropdown, HSOverlay } from 'preline/preline';

// Firebase imports - Realtime Database + Messaging
import { initializeApp } from 'firebase/app';
import { getAuth, signInWithCustomToken, onAuthStateChanged, signOut } from 'firebase/auth';
import { getDatabase, ref, set, get, remove, query, orderByChild, limitToLast, onValue, update, push as firebasePush } from 'firebase/database';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

window.Alpine = Alpine;
Alpine.start();

// Initialize Preline components after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Auto-initialize all Preline components
    HSDropdown.autoInit();
    HSOverlay.autoInit();
});

// Also reinitialize on Livewire/Turbo navigation if used
document.addEventListener('livewire:navigated', () => {
    HSDropdown.autoInit();
    HSOverlay.autoInit();
});

// ============================================
// Firebase Configuration & Initialization
// ============================================

let firebaseApp = null;
let firebaseAuth = null;
let firebaseDb = null;
let firebaseMessaging = null;
let currentFirebaseUser = null;
let notificationUnsubscribe = null;

/**
 * Initialize Firebase with config from window.firebaseConfig (set in Blade)
 */
window.initFirebase = function(config) {
    if (firebaseApp) return; // Already initialized
    
    try {
        firebaseApp = initializeApp(config);
        firebaseAuth = getAuth(firebaseApp);
        firebaseDb = getDatabase(firebaseApp);
        
        // Initialize Firebase Cloud Messaging
        try {
            firebaseMessaging = getMessaging(firebaseApp);
            console.log('🔥 Firebase Messaging initialized');
        } catch (e) {
            console.warn('Firebase Messaging not available:', e.message);
        }
        
        // Listen for auth state changes
        onAuthStateChanged(firebaseAuth, (user) => {
            currentFirebaseUser = user;
            if (user) {
                console.log('🔥 Firebase authenticated:', user.uid);
                // Start listening for notifications when authenticated
                startNotificationListener();
                // Request FCM token
                requestFcmPermission();
            } else {
                console.log('🔥 Firebase signed out');
                // Stop listening for notifications when signed out
                stopNotificationListener();
            }
        });
        
        console.log('🔥 Firebase initialized (Realtime Database)');
    } catch (error) {
        console.error('Firebase init error:', error);
    }
};

/**
 * Sign in to Firebase using custom token from Laravel backend
 */
window.signInToFirebase = async function() {
    if (!firebaseAuth) {
        console.error('Firebase not initialized');
        return false;
    }
    
    try {
        // Use web route (not API) for session-based auth
        const response = await fetch('/firebase-token', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            console.error('Failed to get Firebase token');
            return false;
        }
        
        const data = await response.json();
        await signInWithCustomToken(firebaseAuth, data.token);
        return true;
        
    } catch (error) {
        console.error('Firebase sign in failed:', error);
        return false;
    }
};

/**
 * Sign out from Firebase
 */
window.signOutFirebase = async function() {
    if (firebaseAuth) {
        await signOut(firebaseAuth);
    }
};

/**
 * Check if user is authenticated with Firebase
 */
window.isFirebaseAuthenticated = function() {
    return currentFirebaseUser !== null;
};

// ============================================
// Watch Progress Functions (Firebase Only)
// ============================================

/**
 * Save watch progress to Firebase Realtime Database only
 */
window.saveWatchProgress = async function(mediaId, progressData) {
    // Only save to Firebase when authenticated
    if (!firebaseDb || !currentFirebaseUser) {
        console.warn('Watch progress not saved: User not authenticated with Firebase');
        return;
    }
    
    try {
        const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress/${mediaId}`);
        
        await set(progressRef, {
            ...progressData,
            last_updated: Date.now()
        });
        
    } catch (error) {
        console.error('Firebase save error:', error);
    }
};

/**
 * Get watch progress from Firebase only
 */
window.getWatchProgress = async function(mediaId) {
    // Only get from Firebase when authenticated
    if (!firebaseDb || !currentFirebaseUser) {
        return null;
    }
    
    try {
        const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress/${mediaId}`);
        const snapshot = await get(progressRef);
        
        if (snapshot.exists()) {
            return snapshot.val();
        }
    } catch (error) {
        console.error('Firebase get error:', error);
    }
    
    return null;
};

/**
 * Get all continue watching items (Firebase only)
 */
window.getContinueWatching = async function(maxItems = 10) {
    // Only get from Firebase when authenticated
    if (!firebaseDb || !currentFirebaseUser) {
        return [];
    }
    
    try {
        const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress`);
        const snapshot = await get(progressRef);
        
        if (snapshot.exists()) {
            const data = snapshot.val();
            return Object.entries(data)
                .map(([id, item]) => ({ id, ...item }))
                .filter(item => {
                    if (!item.progress) return false;
                    const percent = (item.progress.watched / item.progress.duration) * 100;
                    return percent >= 1 && percent < 95;
                })
                .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
                .slice(0, maxItems);
        }
    } catch (error) {
        console.error('Firebase query error:', error);
    }
    
    return [];
};

/**
 * Remove item from watch progress (Firebase only)
 */
window.removeWatchProgress = async function(mediaId) {
    // Only remove from Firebase when authenticated
    if (!firebaseDb || !currentFirebaseUser) {
        return;
    }
    
    try {
        const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress/${mediaId}`);
        await remove(progressRef);
    } catch (error) {
        console.error('Firebase delete error:', error);
    }
};

/**
 * Clear all watch progress (Firebase only)
 */
window.clearAllWatchProgress = async function() {
    // Only clear from Firebase when authenticated
    if (!firebaseDb || !currentFirebaseUser) {
        return;
    }
    
    try {
        const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress`);
        await remove(progressRef);
    } catch (error) {
        console.error('Firebase clear error:', error);
    }
};

/**
 * Get ALL watch history items (Firebase only)
 */
window.getAllWatchHistory = async function() {
    // Only get from Firebase when authenticated
    if (!firebaseDb || !currentFirebaseUser) {
        return [];
    }
    
    try {
        const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress`);
        const snapshot = await get(progressRef);
        
        if (snapshot.exists()) {
            const data = snapshot.val();
            return Object.entries(data)
                .map(([id, item]) => ({ id, ...item }))
                .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0));
        }
    } catch (error) {
        console.error('Firebase get all error:', error);
    }
    
    return [];
};

// ============================================
// Notification Functions (Firebase)
// ============================================

/**
 * Start listening for notifications in real-time
 */
function startNotificationListener() {
    if (!firebaseDb || !currentFirebaseUser || notificationUnsubscribe) return;
    
    const notifPath = `notifications/${currentFirebaseUser.uid}`;
    console.log('🔔 Listening to path:', notifPath);
    const notifRef = ref(firebaseDb, notifPath);
    
    notificationUnsubscribe = onValue(notifRef, (snapshot) => {
        console.log('🔔 Notification snapshot received:', snapshot.exists(), snapshot.val());
        if (snapshot.exists()) {
            const notifications = [];
            snapshot.forEach((child) => {
                notifications.push({
                    id: child.key,
                    ...child.val()
                });
            });
            
            // Sort by timestamp descending
            notifications.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
            
            console.log('🔔 Parsed notifications:', notifications);
            
            // Update the notification store
            updateNotificationStore(notifications);
        } else {
            console.log('🔔 No notifications found');
            updateNotificationStore([]);
        }
    }, (error) => {
        console.error('🔔 Notification listener error:', error);
    });
    
    console.log('🔔 Notification listener started');
}

/**
 * Stop listening for notifications
 */
function stopNotificationListener() {
    if (notificationUnsubscribe) {
        notificationUnsubscribe();
        notificationUnsubscribe = null;
        console.log('🔔 Notification listener stopped');
    }
}

/**
 * Update notification store (Alpine reactive)
 */
function updateNotificationStore(notifications) {
    // Dispatch custom event for Alpine components
    window.dispatchEvent(new CustomEvent('notifications-updated', {
        detail: { notifications }
    }));
    
    // Store globally for easy access
    window.userNotifications = notifications;
    window.unreadNotificationCount = notifications.filter(n => !n.read).length;
    
    // Update badge if element exists
    const badge = document.getElementById('notification-badge');
    if (badge) {
        const unread = window.unreadNotificationCount;
        badge.textContent = unread > 99 ? '99+' : unread;
        badge.classList.toggle('hidden', unread === 0);
    }
}

/**
 * Get all notifications for current user
 */
window.getNotifications = async function() {
    if (!firebaseDb || !currentFirebaseUser) {
        return [];
    }
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}`);
        const snapshot = await get(notifRef);
        
        if (snapshot.exists()) {
            const notifications = [];
            snapshot.forEach((child) => {
                notifications.push({
                    id: child.key,
                    ...child.val()
                });
            });
            return notifications.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
        }
    } catch (error) {
        console.error('Error getting notifications:', error);
    }
    
    return [];
};

/**
 * Mark notification as read
 */
window.markNotificationRead = async function(notificationId) {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}/${notificationId}`);
        await update(notifRef, { read: true });
    } catch (error) {
        console.error('Error marking notification read:', error);
    }
};

/**
 * Mark all notifications as read
 */
window.markAllNotificationsRead = async function() {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifications = window.userNotifications || [];
        const updates = {};
        
        notifications.forEach(n => {
            if (!n.read) {
                updates[`notifications/${currentFirebaseUser.uid}/${n.id}/read`] = true;
            }
        });
        
        if (Object.keys(updates).length > 0) {
            await update(ref(firebaseDb), updates);
        }
    } catch (error) {
        console.error('Error marking all notifications read:', error);
    }
};

/**
 * Delete a notification
 */
window.deleteNotification = async function(notificationId) {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}/${notificationId}`);
        await remove(notifRef);
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
};

/**
 * Clear all notifications
 */
window.clearAllNotifications = async function() {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}`);
        await remove(notifRef);
    } catch (error) {
        console.error('Error clearing notifications:', error);
    }
};

// ============================================
// FCM Token Functions
// ============================================

/**
 * Request notification permission and get FCM token
 */
async function requestFcmPermission() {
    if (!firebaseMessaging || !currentFirebaseUser) return;
    
    try {
        // Check if notifications are supported
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications');
            return;
        }
        
        // Request permission
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('🔔 Notification permission granted');
            await getFcmToken();
        } else {
            console.log('🔔 Notification permission denied');
        }
    } catch (error) {
        console.error('Error requesting notification permission:', error);
    }
}

/**
 * Get FCM token and save to Firebase
 */
async function getFcmToken() {
    if (!firebaseMessaging || !currentFirebaseUser) return;
    
    // Skip FCM in development (self-signed SSL certificates cause issues)
    const hostname = window.location.hostname;
    if (hostname === 'localhost' || hostname.endsWith('.test') || hostname.endsWith('.local')) {
        console.log('🔔 FCM skipped in development environment');
        return;
    }
    
    try {
        // Get FCM token - uses VAPID key if provided
        const vapidKey = window.firebaseVapidKey || null;
        
        const token = await getToken(firebaseMessaging, {
            vapidKey: vapidKey,
            serviceWorkerRegistration: await navigator.serviceWorker.register('/firebase-messaging-sw.js')
        });
        
        if (token) {
            console.log('🔔 FCM Token obtained');
            // Save token to Firebase
            await saveFcmToken(token);
        }
    } catch (error) {
        console.error('Error getting FCM token:', error);
    }
}

/**
 * Save FCM token to Firebase for the current user
 */
async function saveFcmToken(token) {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const tokenRef = ref(firebaseDb, `fcmTokens/${currentFirebaseUser.uid}/${token.substring(0, 20)}`);
        await set(tokenRef, {
            token: token,
            createdAt: Date.now(),
            platform: navigator.platform,
            userAgent: navigator.userAgent.substring(0, 100)
        });
        console.log('🔔 FCM Token saved to Firebase');
    } catch (error) {
        console.error('Error saving FCM token:', error);
    }
}

/**
 * Handle foreground messages
 */
window.setupForegroundMessages = function() {
    if (!firebaseMessaging) return;
    
    onMessage(firebaseMessaging, (payload) => {
        console.log('🔔 Foreground message received:', payload);
        
        // Show a toast notification
        showToastNotification(payload);
    });
};

/**
 * Show toast notification for foreground messages
 */
function showToastNotification(payload) {
    const title = payload.notification?.title || payload.data?.title || 'New Notification';
    const body = payload.notification?.body || payload.data?.body || '';
    const image = payload.data?.image || null;
    const link = payload.data?.link || '/';
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 z-[9999] max-w-sm bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-4 transform translate-x-full transition-transform duration-300';
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            ${image ? `<img src="${image}" class="w-12 h-12 rounded-lg object-cover" alt="">` : ''}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">${title}</p>
                <p class="text-xs text-gray-400 line-clamp-2">${body}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Click to navigate
    toast.addEventListener('click', (e) => {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'SVG' && e.target.tagName !== 'PATH') {
            window.location.href = link;
        }
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}
