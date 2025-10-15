<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simuler la logique de filtrage
$supportUsers = \App\Models\User::where('company_id', 1)
    ->where('status', 'active')
    ->get()
    ->filter(function ($user) {
        $channels = $user->channels ?? [];
        return !empty($channels['email']);
    });

echo "📧 Utilisateurs qui recevront l'email: {$supportUsers->count()}\n";
foreach ($supportUsers as $user) {
    echo "  - {$user->name} ({$user->email})\n";
}

$pushoverUsers = \App\Models\User::where('company_id', 1)
    ->where('status', 'active')
    ->get()
    ->filter(function ($user) {
        $channels = $user->channels ?? [];
        return !empty($channels['sms']);
    });

echo "\n🔔 Utilisateurs qui recevront Pushover: {$pushoverUsers->count()}\n";
foreach ($pushoverUsers as $user) {
    echo "  - {$user->name}\n";
}
