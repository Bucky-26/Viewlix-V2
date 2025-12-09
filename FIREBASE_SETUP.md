# Firebase Setup for Viewlix

This guide explains how to set up Firebase for watch history synchronization with your Laravel authentication.

## How It Works

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   User Login    │────▶│  Laravel Auth   │────▶│ Firebase Custom │
│   (Your Site)   │     │   (Backend)     │     │     Token       │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                         │
                                                         ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Watch History  │◀───▶│   Firestore     │◀────│ Firebase Auth   │
│   (Frontend)    │     │   Database      │     │ (Custom Token)  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

**Security**: Each user can ONLY access their own data because:
1. Laravel creates a custom Firebase token with the user's ID
2. Firestore security rules check `request.auth.uid == userId`

---

## Step 1: Create Firebase Project

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Click **"Create a project"** or **"Add project"**
3. Name it `viewlix` (or your preferred name)
4. Disable Google Analytics (optional for this use case)
5. Click **Create project**

---

## Step 2: Enable Firestore Database

1. In Firebase Console, go to **Build → Firestore Database**
2. Click **Create database**
3. Choose **Start in production mode**
4. Select your preferred region (e.g., `us-central1`)
5. Click **Enable**

---

## Step 3: Set Up Security Rules

1. Go to **Firestore Database → Rules**
2. Replace the default rules with the contents of `firestore.rules` file:

```
rules_version = '2';

service cloud.firestore {
  match /databases/{database}/documents {
    match /users/{userId} {
      allow read, write: if request.auth != null && request.auth.uid == userId;
      
      match /watchProgress/{mediaId} {
        allow read, write: if request.auth != null && request.auth.uid == userId;
      }
    }
    
    match /{document=**} {
      allow read, write: if false;
    }
  }
}
```

3. Click **Publish**

---

## Step 4: Generate Service Account Key (for Laravel Backend)

1. Go to **Project Settings** (gear icon) → **Service accounts**
2. Click **Generate new private key**
3. Save the JSON file as `service-account.json`
4. Move it to your Laravel project:

```
storage/app/firebase/service-account.json
```

⚠️ **IMPORTANT**: Add this path to `.gitignore`:
```
storage/app/firebase/
```

---

## Step 5: Get Web App Config (for Frontend)

1. Go to **Project Settings → General**
2. Scroll to **Your apps** → Click **Web** (</> icon)
3. Register your app (name: `viewlix-web`)
4. Copy the Firebase config object:

```javascript
const firebaseConfig = {
  apiKey: "AIza...",
  authDomain: "viewlix-xxxxx.firebaseapp.com",
  projectId: "viewlix-xxxxx",
  storageBucket: "viewlix-xxxxx.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abcdef"
};
```

---

## Step 6: Add Config to Your Laravel .env

```env
# Firebase Configuration (Frontend)
FIREBASE_API_KEY="AIza..."
FIREBASE_AUTH_DOMAIN="viewlix-xxxxx.firebaseapp.com"
FIREBASE_PROJECT_ID="viewlix-xxxxx"
FIREBASE_STORAGE_BUCKET="viewlix-xxxxx.appspot.com"
FIREBASE_MESSAGING_SENDER_ID="123456789"
FIREBASE_APP_ID="1:123456789:web:abcdef"
```

---

## Step 7: Install Firebase JS SDK

Run in your project root:

```bash
npm install firebase
```

---

## Step 8: Update Your Blade Layout

Add this to your `layouts/app.blade.php` before the closing `</body>` tag:

```html
@auth
<script type="module">
    import { initFirebase, signInWithLaravelToken, syncLocalToFirestore } from '/build/assets/firebase.js';
    
    // Initialize Firebase
    initFirebase({
        apiKey: "{{ config('services.firebase.api_key') }}",
        authDomain: "{{ config('services.firebase.auth_domain') }}",
        projectId: "{{ config('services.firebase.project_id') }}",
        storageBucket: "{{ config('services.firebase.storage_bucket') }}",
        messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
        appId: "{{ config('services.firebase.app_id') }}"
    });
    
    // Sign in to Firebase with Laravel token
    signInWithLaravelToken().then(success => {
        if (success) {
            // Sync any localStorage progress to Firestore
            syncLocalToFirestore();
        }
    });
</script>
@endauth
```

---

## Step 9: Add Firebase Config to Laravel

Create/update `config/services.php`:

```php
'firebase' => [
    'api_key' => env('FIREBASE_API_KEY'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
    'app_id' => env('FIREBASE_APP_ID'),
],
```

---

## Testing

1. Log in to your app
2. Watch a movie/show for a few seconds
3. Check Firebase Console → Firestore Database
4. You should see: `users/{your-laravel-user-id}/watchProgress/{media-id}`

---

## Security Checklist

- [ ] `service-account.json` is in `.gitignore`
- [ ] Firestore security rules are published
- [ ] Firebase config is in `.env` (not hardcoded)
- [ ] Custom token endpoint requires authentication

---

## Troubleshooting

### "Permission denied" error
- Check Firestore security rules are published
- Verify the user is authenticated (check browser console)

### "Firebase not configured" error
- Ensure `service-account.json` exists in `storage/app/firebase/`
- Check file permissions

### Token not working
- Clear Laravel cache: `php artisan config:clear`
- Check Laravel logs: `storage/logs/laravel.log`
