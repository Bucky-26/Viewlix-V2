/**
 * Firebase Configuration for Developer Blog
 * This module handles Firebase initialization for the developer blog module
 */

// Firebase SDK v7.20.0+ configuration
const developerBlogFirebaseConfig = {
  apiKey: "AIzaSyDEviz_seX3BI9C84A_hBlX58cNcxxpuL4",
  authDomain: "viewlix-dev-blogdb.firebaseapp.com",
  projectId: "viewlix-dev-blogdb",
  storageBucket: "viewlix-dev-blogdb.firebasestorage.app",
  messagingSenderId: "559675863252",
  appId: "1:559675863252:web:73f929e6a1c43f36840b41",
  measurementId: "G-YTEL3656CC"
};

/**
 * Initialize Firebase for Developer Blog
 * This is called on pages that need Firebase integration
 */
export function initializeDeveloperBlogFirebase() {
  // Initialize Firebase if needed
  if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length > 0) {
    console.log('Firebase already initialized');
    return firebase.apps[0];
  }

  // This requires the Firebase SDK to be loaded globally
  if (typeof firebase === 'undefined') {
    console.error('Firebase SDK not loaded. Include <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js"></script>');
    return null;
  }

  try {
    const app = firebase.initializeApp(developerBlogFirebaseConfig);
    console.log('Developer Blog Firebase initialized successfully');
    return app;
  } catch (error) {
    console.error('Error initializing Firebase:', error);
    return null;
  }
}

/**
 * Get Firestore instance for blog data
 */
export function getDeveloperBlogFirestore() {
  const app = initializeDeveloperBlogFirebase();
  if (!app) return null;
  
  if (typeof firebase.firestore !== 'function') {
    console.error('Firestore SDK not loaded');
    return null;
  }

  return firebase.firestore();
}

/**
 * Get Realtime Database instance for blog data
 */
export function getDeveloperBlogDatabase() {
  const app = initializeDeveloperBlogFirebase();
  if (!app) return null;
  
  if (typeof firebase.database !== 'function') {
    console.error('Realtime Database SDK not loaded');
    return null;
  }

  return firebase.database();
}

/**
 * Get Storage instance for blog images
 */
export function getDeveloperBlogStorage() {
  const app = initializeDeveloperBlogFirebase();
  if (!app) return null;
  
  if (typeof firebase.storage !== 'function') {
    console.error('Storage SDK not loaded');
    return null;
  }

  return firebase.storage();
}

/**
 * Track blog post view in Firebase
 */
export async function trackBlogPostView(postId, postSlug) {
  try {
    const db = getDeveloperBlogDatabase();
    if (!db) {
      console.warn('Firebase not configured for blog tracking');
      return;
    }

    const timestamp = new Date().toISOString();
    const ref = db.ref(`blog_analytics/posts/${postId}/views`);
    
    await ref.push({
      timestamp: timestamp,
      slug: postSlug,
      userAgent: navigator.userAgent
    });
  } catch (error) {
    console.error('Error tracking blog post view:', error);
  }
}

/**
 * Get blog post analytics
 */
export async function getBlogPostAnalytics(postId) {
  try {
    const db = getDeveloperBlogDatabase();
    if (!db) return null;

    const ref = db.ref(`blog_analytics/posts/${postId}/views`);
    const snapshot = await ref.once('value');
    
    return snapshot.val() || {};
  } catch (error) {
    console.error('Error getting blog post analytics:', error);
    return null;
  }
}

export default {
  config: developerBlogFirebaseConfig,
  initializeDeveloperBlogFirebase,
  getDeveloperBlogFirestore,
  getDeveloperBlogDatabase,
  getDeveloperBlogStorage,
  trackBlogPostView,
  getBlogPostAnalytics
};
