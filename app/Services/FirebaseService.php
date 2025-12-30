<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Auth;

class FirebaseService
{
    protected $auth;
    protected $factory;

    public function __construct()
    {
        $serviceAccountPath = storage_path('app/firebase/service-account.json');
        
        if (file_exists($serviceAccountPath)) {
            $this->factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $this->auth = $this->factory->createAuth();
        }
    }

    /**
     * Generate a custom Firebase token for the authenticated Laravel user
     * Uses the user's firebase_uuid for security (doesn't expose internal user IDs)
     */
    public function createCustomToken(): ?string
    {
        if (!$this->auth) {
            return null;
        }

        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        try {
            // Get or create the user's Firebase UUID
            $firebaseUuid = $user->getFirebaseUuid();
            
            // Create a custom token with the Firebase UUID
            // This links their Firebase session to their Laravel account securely
            $customToken = $this->auth->createCustomToken($firebaseUuid, [
                'email' => $user->email,
                'name' => $user->name,
            ]);

            return $customToken->toString();
        } catch (\Exception $e) {
            \Log::error('Firebase custom token creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Firebase Auth instance
     */
    public function getAuth(): ?FirebaseAuth
    {
        return $this->auth;
    }

    /**
     * Get a Realtime Database instance (if configured)
     * @return \Kreait\Firebase\Database|null
     */
    public function getDatabase()
    {
        if (!$this->factory) {
            return null;
        }

        try {
            return $this->factory->createDatabase();
        } catch (\Throwable $e) {
            \Log::error('Firebase database create failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Read a path from the Realtime Database and return its value as array/null
     */
    public function getDatabasePath(string $path)
    {
        $db = $this->getDatabase();

        if (!$db) {
            return null;
        }

        try {
            $ref = $db->getReference($path);
            $value = $ref->getValue();
            return $value;
        } catch (\Throwable $e) {
            \Log::error('Firebase getDatabasePath failed for ' . $path . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if Firebase is configured
     */
    public function isConfigured(): bool
    {
        return $this->auth !== null;
    }
}
