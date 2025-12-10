// Firebase Messaging Service Worker for Viewlix
// This handles background push notifications

importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

// Firebase configuration for viewlix-v2
firebase.initializeApp({
    apiKey: "AIzaSyAJjI3k_vfHexdpRzZPR0Ft0BUX1e3Zw8w",
    authDomain: "viewlix-v2.firebaseapp.com",
    databaseURL: "https://viewlix-v2-default-rtdb.firebaseio.com",
    projectId: "viewlix-v2",
    storageBucket: "viewlix-v2.firebasestorage.app",
    messagingSenderId: "706887870364",
    appId: "1:706887870364:web:dc4d637fc3048aa2e68a59"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message:', payload);

    const notificationTitle = payload.notification?.title || payload.data?.title || 'Viewlix';
    const notificationOptions = {
        body: payload.notification?.body || payload.data?.body || 'You have a new notification',
        icon: payload.notification?.icon || payload.data?.image || '/logo.png',
        badge: '/logo.png',
        image: payload.data?.image || null,
        tag: payload.data?.id || 'viewlix-notification',
        data: {
            url: payload.data?.link || '/',
            notificationId: payload.data?.id
        },
        actions: [
            {
                action: 'open',
                title: 'View'
            },
            {
                action: 'dismiss',
                title: 'Dismiss'
            }
        ],
        vibrate: [200, 100, 200],
        requireInteraction: true
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification click received:', event);

    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    const urlToOpen = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Check if there's already a window open
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.focus();
                    client.postMessage({
                        type: 'NOTIFICATION_CLICKED',
                        url: urlToOpen,
                        notificationId: event.notification.data?.notificationId
                    });
                    return;
                }
            }
            // Open a new window if none exists
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Handle push event for custom payloads
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        console.log('[firebase-messaging-sw.js] Push event received:', data);
    }
});
