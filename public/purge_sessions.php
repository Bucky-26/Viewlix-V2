<?php

// A simple vanilla PHP script to purge all Laravel session files
// Place this in your public/ folder as purge_sessions.php and visit it in your browser.

$sessionDir = __DIR__ . '/../storage/framework/sessions';

// Check if directory exists
if (!is_dir($sessionDir)) {
    die("Session directory not found at: $sessionDir");
}

// Get all files in the directory, excluding hidden files like .gitignore
$files = array_diff(scandir($sessionDir), array('.', '..', '.gitignore'));

$deletedCount = 0;

foreach ($files as $file) {
    $filePath = $sessionDir . '/' . $file;
    
    // Make sure we only delete regular files
    if (is_file($filePath)) {
        unlink($filePath);
        $deletedCount++;
    }
}

echo "<h1>Success!</h1>";
echo "<p>Purged <strong>$deletedCount</strong> session files from storage.</p>";
