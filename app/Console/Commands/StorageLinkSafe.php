<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StorageLinkSafe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link-safe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the public/storage symlink or fall back to copying when symlink() is disabled.';

    public function handle()
    {
        $publicPath = public_path();
        $link = $publicPath . DIRECTORY_SEPARATOR . 'storage';
        $target = base_path('storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public');

        if (file_exists($link) || is_link($link)) {
            $this->info("Link or directory already exists at: {$link}");
            return 0;
        }

        // Try real symlink first
        if (function_exists('symlink')) {
            try {
                symlink($target, $link);
                $this->info("Created symlink: public/storage -> {$target}");
                return 0;
            } catch (\Throwable $e) {
                $this->warn('symlink() exists but failed: ' . $e->getMessage());
            }
        } else {
            $this->warn('symlink() is not available in this PHP environment.');
        }

        // Fallback: copy files into public/storage
        $this->info('Falling back to copying files into public/storage.');

        if (!is_dir($target)) {
            $this->error("Source directory not found: {$target}");
            return 1;
        }

        if (!mkdir($link, 0755, true) && !is_dir($link)) {
            $this->error("Unable to create destination directory: {$link}");
            return 1;
        }

        $this->copyDirectory($target, $link);
        $this->info('Files copied to public/storage. Add a cron or deploy step to keep them synced (rsync recommended).');

        return 0;
    }

    protected function copyDirectory(string $src, string $dst): void
    {
        $dir = opendir($src);
        if ($dir === false) {
            return;
        }

        while (false !== ($file = readdir($dir))) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;

            if (is_dir($srcPath)) {
                if (!is_dir($dstPath)) {
                    mkdir($dstPath, 0755, true);
                }
                $this->copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }

        closedir($dir);
    }
}
