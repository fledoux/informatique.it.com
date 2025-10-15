<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST COMPLET NOTIFICATIONS ===\n\n";

// 1. Vérifier les utilisateurs qui recevront les notifications
echo "1️⃣ Utilisateurs company_id=1 actifs:\n";
$allUsers = \App\Models\User::where('company_id', 1)->where('status', 'active')->get();
echo "   Total: {$allUsers->count()}\n\n";

foreach ($allUsers as $user) {
    $channels = $user->channels ?? [];
    echo "   - {$user->name} ({$user->email})\n";
    echo "     Email: " . (!empty($channels['email']) ? '✅' : '❌') . "\n";
    echo "     SMS/Pushover: " . (!empty($channels['sms']) ? '✅' : '❌') . "\n\n";
}

// 2. Filtrer pour email
echo "2️⃣ Qui recevra les EMAILS:\n";
$supportUsers = \App\Models\User::where('company_id', 1)
    ->where('status', 'active')
    ->get()
    ->filter(function ($user) {
        $channels = $user->channels ?? [];
        return !empty($channels['email']);
    });
echo "   Count: {$supportUsers->count()}\n";
foreach ($supportUsers as $user) {
    echo "   → {$user->email}\n";
}

// 3. Filtrer pour Pushover
echo "\n3️⃣ Qui recevra PUSHOVER:\n";
$pushoverUsers = \App\Models\User::where('company_id', 1)
    ->where('status', 'active')
    ->get()
    ->filter(function ($user) {
        $channels = $user->channels ?? [];
        return !empty($channels['sms']);
    });
echo "   Count: {$pushoverUsers->count()}\n";
foreach ($pushoverUsers as $user) {
    echo "   → {$user->name}\n";
}

// 4. Vérifier la config Pushover
echo "\n4️⃣ Configuration Pushover:\n";
echo "   User Key: " . (config('services.pushover.user') ? '✅ Configuré' : '❌ Manquant') . "\n";
echo "   API Token: " . (config('services.pushover.token') ? '✅ Configuré' : '❌ Manquant') . "\n";

// 5. Vérifier la config Email
echo "\n5️⃣ Configuration Email:\n";
echo "   Mailer: " . config('mail.default') . "\n";
echo "   From: " . config('mail.from.address') . "\n";

echo "\n✅ Test terminé !\n";
