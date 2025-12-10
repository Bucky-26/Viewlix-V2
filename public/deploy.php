<?php

/**
 * Viewlix Deployment Helper for Hostinger
 * 
 * This script helps run Laravel artisan commands on hosting without SSH.
 * 
 * SECURITY: Delete this file after initial setup!
 * 
 * Usage: https://yourdomain.com/deploy.php?key=YOUR_SECRET_KEY&action=ACTION
 */

// ============================================
// CHANGE THIS SECRET KEY!
// ============================================
$secretKey = 'viewlix-deploy-' . md5('change-this-secret-phrase');

// Verify secret key
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Unauthorized. Invalid or missing key.');
}

// Set working directory to Laravel root
chdir(__DIR__);

// Get action
$action = $_GET['action'] ?? 'help';

header('Content-Type: text/plain; charset=utf-8');

echo "===========================================\n";
echo "Viewlix Deployment Helper\n";
echo "===========================================\n\n";

switch ($action) {
    case 'composer':
        echo "Running composer install...\n";
        echo shell_exec('cd .. && composer install --optimize-autoloader --no-dev 2>&1');
        break;

    case 'migrate':
        echo "Running migrations...\n";
        echo shell_exec('php artisan migrate --force 2>&1');
        break;

    case 'seed':
        echo "Running seeders...\n";
        echo shell_exec('php artisan db:seed --force 2>&1');
        break;

    case 'migrate-fresh':
        echo "WARNING: This will DROP all tables!\n";
        echo shell_exec('php artisan migrate:fresh --force 2>&1');
        break;

    case 'storage-link':
        echo "Creating storage link...\n";
        echo shell_exec('php artisan storage:link 2>&1');
        break;

    case 'cache-clear':
        echo "Clearing all caches...\n";
        echo shell_exec('php artisan config:clear 2>&1');
        echo shell_exec('php artisan cache:clear 2>&1');
        echo shell_exec('php artisan view:clear 2>&1');
        echo shell_exec('php artisan route:clear 2>&1');
        break;

    case 'optimize':
        echo "Optimizing application...\n";
        echo shell_exec('php artisan config:cache 2>&1');
        echo shell_exec('php artisan route:cache 2>&1');
        echo shell_exec('php artisan view:cache 2>&1');
        break;

    case 'key-generate':
        echo "Generating application key...\n";
        echo shell_exec('php artisan key:generate --show 2>&1');
        echo "\nCopy this key to your .env file as APP_KEY\n";
        break;

    case 'status':
        echo "Application Status:\n";
        echo "-------------------\n";
        echo "PHP Version: " . phpversion() . "\n";
        echo "Laravel: ";
        echo shell_exec('php artisan --version 2>&1');
        echo "\nEnvironment: ";
        echo shell_exec('php artisan env 2>&1');
        echo "\nStorage writable: " . (is_writable('storage') ? 'Yes' : 'No') . "\n";
        echo "Bootstrap/cache writable: " . (is_writable('bootstrap/cache') ? 'Yes' : 'No') . "\n";
        echo ".env exists: " . (file_exists('.env') ? 'Yes' : 'No') . "\n";
        echo "vendor exists: " . (is_dir('vendor') ? 'Yes' : 'No') . "\n";
        echo "public/build exists: " . (is_dir('public/build') ? 'Yes' : 'No') . "\n";
        echo "Firebase service account: " . (file_exists('storage/app/firebase/service-account.json') ? 'Yes' : 'No') . "\n";
        break;

    case 'deploy':
        echo "Full deployment process...\n\n";
        
        echo "1. Clearing caches...\n";
        echo shell_exec('php artisan config:clear 2>&1');
        echo shell_exec('php artisan cache:clear 2>&1');
        echo shell_exec('php artisan view:clear 2>&1');
        echo shell_exec('php artisan route:clear 2>&1');
        
        echo "\n2. Running migrations...\n";
        echo shell_exec('php artisan migrate --force 2>&1');
        
        echo "\n3. Creating storage link...\n";
        echo shell_exec('php artisan storage:link 2>&1');
        
        echo "\n4. Optimizing...\n";
        echo shell_exec('php artisan config:cache 2>&1');
        echo shell_exec('php artisan route:cache 2>&1');
        echo shell_exec('php artisan view:cache 2>&1');
        
        echo "\n✅ Deployment complete!\n";
        break;

    case 'help':
    default:
        echo "Available actions:\n";
        echo "------------------\n";
        echo "?action=status        - Check application status\n";
        echo "?action=deploy        - Full deployment (clear cache, migrate, optimize)\n";
        echo "?action=migrate       - Run database migrations\n";
        echo "?action=seed          - Run database seeders\n";
        echo "?action=storage-link  - Create storage symlink\n";
        echo "?action=cache-clear   - Clear all caches\n";
        echo "?action=optimize      - Cache config, routes, views\n";
        echo "?action=key-generate  - Generate new APP_KEY\n";
        echo "\nExample: deploy.php?key=YOUR_KEY&action=deploy\n";
        echo "\n⚠️  DELETE THIS FILE AFTER SETUP!\n";
        break;
}

echo "\n===========================================\n";
echo "Done at " . date('Y-m-d H:i:s') . "\n";
