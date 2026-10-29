<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register storage:link-safe wrapper so it is available even if Kernel isn't modified.
Artisan::command('storage:link-safe', function () {
    $cmd = new \App\Console\Commands\StorageLinkSafe();
    $exit = $cmd->handle();
    return $exit === 0 ? 0 : 1;
})->purpose('Create public/storage symlink or fall back to copying when symlink() is disabled');

