<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(1);
if (! $user) {
    echo "NO_USER\n";
    exit(1);
}

$token = $user->createToken('cli-token')->plainTextToken;
echo $token . PHP_EOL;
