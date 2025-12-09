/**
 * Viewlix Firebase Integration
 * Handles watch progress sync with Firebase Firestore
 * Uses custom authentication linked to Laravel backend
 */

import { initializeApp } from 'firebase/app';
import { getAuth, signInWithCustomToken, onAuthStateChanged } from 'firebase/auth';
import { getFirestore, doc, setDoc, getDoc, collection, query, orderBy, limit, getDocs, deleteDoc, serverTimestamp } from 'firebase/firestore';

// Firebase configuration - will be injected from Blade template
let app = null;
let auth = null;
let db = null;
let currentUser = null;

/**
 * Initialize Firebase with config
 */
export function initFirebase(config) {
    if (app) return; // Already initialized
    
    app = initializeApp(config);
    auth = getAuth(app);
    db = getFirestore(app);
    
    // Listen for auth state changes
    onAuthStateChanged(auth, (user) => {
        currentUser = user;
        if (user) {
            console.log('Firebase authenticated as user:', user.uid);
        }
    });
}

/**
 * Sign in to Firebase using custom token from Laravel
 */
export async function signInWithLaravelToken() {
    if (!auth) {
        console.error('Firebase not initialized');
        return false;
    }
    
    try {
        // Get custom token from Laravel backend
        const response = await fetch('/api/firebase-token', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error('Failed to get Firebase token');
        }
        
        const data = await response.json();
        
        // Sign in to Firebase with the custom token
        await signInWithCustomToken(auth, data.token);
        return true;
        
    } catch (error) {
        console.error('Firebase sign in failed:', error);
        return false;
    }
}

/**
 * Save watch progress to Firestore
 */
export async function saveProgress(mediaId, progressData) {
    if (!db || !currentUser) {
        // Fall back to localStorage if not authenticated
        saveToLocalStorage(mediaId, progressData);
        return;
    }
    
    try {
        const docRef = doc(db, 'users', currentUser.uid, 'watchProgress', String(mediaId));
        
        await setDoc(docRef, {
            ...progressData,
            last_updated: serverTimestamp()
        }, { merge: true });
        
        // Also save to localStorage as backup
        saveToLocalStorage(mediaId, progressData);
        
    } catch (error) {
        console.error('Error saving to Firestore:', error);
        // Fall back to localStorage
        saveToLocalStorage(mediaId, progressData);
    }
}

/**
 * Get watch progress from Firestore
 */
export async function getProgress(mediaId) {
    if (!db || !currentUser) {
        return getFromLocalStorage(mediaId);
    }
    
    try {
        const docRef = doc(db, 'users', currentUser.uid, 'watchProgress', String(mediaId));
        const docSnap = await getDoc(docRef);
        
        if (docSnap.exists()) {
            return docSnap.data();
        }
        
        // Fall back to localStorage
        return getFromLocalStorage(mediaId);
        
    } catch (error) {
        console.error('Error getting from Firestore:', error);
        return getFromLocalStorage(mediaId);
    }
}

/**
 * Get all continue watching items
 */
export async function getContinueWatching(maxItems = 10) {
    if (!db || !currentUser) {
        return getContinueWatchingFromLocalStorage(maxItems);
    }
    
    try {
        const q = query(
            collection(db, 'users', currentUser.uid, 'watchProgress'),
            orderBy('last_updated', 'desc'),
            limit(maxItems)
        );
        
        const querySnapshot = await getDocs(q);
        const items = [];
        
        querySnapshot.forEach((doc) => {
            const data = doc.data();
            // Only include items with progress between 1% and 95%
            if (data.progress) {
                const percent = (data.progress.watched / data.progress.duration) * 100;
                if (percent >= 1 && percent < 95) {
                    items.push({ id: doc.id, ...data });
                }
            }
        });
        
        return items;
        
    } catch (error) {
        console.error('Error getting continue watching:', error);
        return getContinueWatchingFromLocalStorage(maxItems);
    }
}

/**
 * Remove item from watch progress
 */
export async function removeProgress(mediaId) {
    if (db && currentUser) {
        try {
            const docRef = doc(db, 'users', currentUser.uid, 'watchProgress', String(mediaId));
            await deleteDoc(docRef);
        } catch (error) {
            console.error('Error removing from Firestore:', error);
        }
    }
    
    // Also remove from localStorage
    removeFromLocalStorage(mediaId);
}

/**
 * Sync localStorage progress to Firestore (call after login)
 */
export async function syncLocalToFirestore() {
    if (!db || !currentUser) return;
    
    try {
        const localProgress = getAllFromLocalStorage();
        
        for (const [mediaId, data] of Object.entries(localProgress)) {
            const docRef = doc(db, 'users', currentUser.uid, 'watchProgress', mediaId);
            
            // Get existing Firestore data
            const docSnap = await getDoc(docRef);
            
            // Only update if local is newer or Firestore doesn't have it
            if (!docSnap.exists() || 
                (data.last_updated && (!docSnap.data().last_updated || 
                 data.last_updated > docSnap.data().last_updated.toMillis()))) {
                await setDoc(docRef, {
                    ...data,
                    last_updated: serverTimestamp()
                }, { merge: true });
            }
        }
        
        console.log('Synced localStorage to Firestore');
        
    } catch (error) {
        console.error('Error syncing to Firestore:', error);
    }
}

// ============================================
// LocalStorage helpers (fallback/offline)
// ============================================

function saveToLocalStorage(mediaId, data) {
    try {
        const all = getAllFromLocalStorage();
        all[String(mediaId)] = { ...data, last_updated: Date.now() };
        localStorage.setItem('vidLinkProgress', JSON.stringify(all));
    } catch (e) {
        console.error('localStorage save error:', e);
    }
}

function getFromLocalStorage(mediaId) {
    try {
        const all = getAllFromLocalStorage();
        return all[String(mediaId)] || null;
    } catch (e) {
        return null;
    }
}

function getAllFromLocalStorage() {
    try {
        const stored = localStorage.getItem('vidLinkProgress');
        return stored ? JSON.parse(stored) : {};
    } catch (e) {
        return {};
    }
}

function removeFromLocalStorage(mediaId) {
    try {
        const all = getAllFromLocalStorage();
        delete all[String(mediaId)];
        localStorage.setItem('vidLinkProgress', JSON.stringify(all));
    } catch (e) {
        console.error('localStorage remove error:', e);
    }
}

function getContinueWatchingFromLocalStorage(maxItems = 10) {
    try {
        const all = getAllFromLocalStorage();
        return Object.values(all)
            .filter(item => {
                if (!item.progress) return false;
                const percent = (item.progress.watched / item.progress.duration) * 100;
                return percent >= 1 && percent < 95;
            })
            .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
            .slice(0, maxItems);
    } catch (e) {
        return [];
    }
}

// Export for use in Blade templates
window.ViewlixFirebase = {
    initFirebase,
    signInWithLaravelToken,
    saveProgress,
    getProgress,
    getContinueWatching,
    removeProgress,
    syncLocalToFirestore
};
