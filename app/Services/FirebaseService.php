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
        $credentials = self::getCredentials();
        
        if ($credentials) {
            try {
                $this->factory = (new Factory)->withServiceAccount($credentials);

                // If a Realtime Database URL is configured, ensure the factory uses it.
                $databaseUrl = config('services.firebase.database_url');
                if (!empty($databaseUrl)) {
                    $this->factory = $this->factory->withDatabaseUri($databaseUrl);
                }

                $this->auth = $this->factory->createAuth();
            } catch (\Throwable $e) {
                \Log::error('Firebase initialization failed: ' . $e->getMessage());
                $this->factory = null;
                $this->auth = null;
            }
        }
    }

    /**
     * Resolve service account credentials from .env variables, JSON string, or file path.
     * Returns an array, JSON string, or file path suitable for Kreait Factory::withServiceAccount()
     */
    public static function getCredentials()
    {
        $fb = config('services.firebase', []);

        // 1. Individual fields in .env (FIREBASE_CLIENT_EMAIL + FIREBASE_PRIVATE_KEY)
        if (!empty($fb['client_email']) && !empty($fb['private_key'])) {
            $privateKey = str_replace('\n', "\n", $fb['private_key']);
            return [
                'type' => 'service_account',
                'project_id' => $fb['project_id'] ?? null,
                'private_key_id' => $fb['private_key_id'] ?? null,
                'private_key' => $privateKey,
                'client_email' => $fb['client_email'],
                'client_id' => $fb['client_id'] ?? null,
                'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri' => 'https://oauth2.googleapis.com/token',
                'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                'client_x509_cert_url' => $fb['client_email'] ? ('https://www.googleapis.com/robot/v1/metadata/x509/' . urlencode($fb['client_email'])) : null,
            ];
        }

        // 2. Full JSON string in .env (FIREBASE_SERVICE_ACCOUNT_JSON)
        if (!empty($fb['service_account_json'])) {
            $json = trim($fb['service_account_json']);
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return $json;
        }

        // 3. Base64-encoded JSON string in .env (FIREBASE_SERVICE_ACCOUNT_BASE64)
        if (!empty($fb['service_account_base64'])) {
            $decodedJson = base64_decode($fb['service_account_base64']);
            if ($decodedJson) {
                $decoded = json_decode($decodedJson, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        // 4. Custom file path in .env (FIREBASE_CREDENTIALS)
        if (!empty($fb['credentials']) && file_exists($fb['credentials'])) {
            return $fb['credentials'];
        }

        // 5. Default local file path fallback
        $defaultPath = storage_path('app/firebase/service-account.json');
        if (file_exists($defaultPath)) {
            return $defaultPath;
        }

        return null;
    }

    /**
     * Generate a custom Firebase token for the authenticated Laravel user
     * Uses the user's firebase_uuid for security (doesn't expose internal user IDs)
     */
    public function createCustomToken(?\App\Models\User $user = null): ?string
    {
        if (!$this->auth) {
            return null;
        }

        $user = $user ?? Auth::user();
        
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
