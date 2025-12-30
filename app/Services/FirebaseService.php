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

            // If a Realtime Database URL is configured, ensure the factory uses it.
            // Other parts of the app use config('services.firebase.database_url') so we follow the same pattern.
            $databaseUrl = config('services.firebase.database_url');
            if (!empty($databaseUrl)) {
                $this->factory = $this->factory->withDatabaseUri($databaseUrl);
            }

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
     * Read a path from Firestore and return its value as array/null
     * Supports simple paths like:
     * - collection/document
     * - collection/document/collection
     *
     * Example: users/{uid}/watchProgress
     */
    public function getFirestorePath(string $path)
    {
        if (!$this->factory) {
            return null;
        }

        try {
            $firestore = $this->factory->createFirestore();
            $db = $firestore->database(); // Google\Cloud\Firestore\FirestoreClient

            $parts = array_values(array_filter(explode('/', trim($path, '/'))));
            $count = count($parts);

            if ($count === 2) {
                // collection/document -> return document data
                [$collection, $docId] = $parts;
                $snapshot = $db->collection($collection)->document($docId)->snapshot();
                if ($snapshot->exists()) {
                    return $snapshot->data();
                }
                return null;
            }

            if ($count === 3) {
                // collection/document/collection -> return all documents in the child collection
                [$collection, $docId, $childCollection] = $parts;
                $documents = $db->collection($collection)->document($docId)->collection($childCollection)->documents();
                $result = [];
                foreach ($documents as $doc) {
                    if ($doc->exists()) {
                        $result[$doc->id()] = $doc->data();
                    }
                }
                return $result;
            }

            // Unsupported depth: return null to keep behavior predictable
            return null;
        } catch (\Throwable $e) {
            \Log::error('Firebase getFirestorePath failed for ' . $path . ': ' . $e->getMessage());
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
