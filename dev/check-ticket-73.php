<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ticket = \App\Models\Ticket::find(73);
echo "Ticket #{$ticket->id}: {$ticket->subject}\n";
echo "Reply code: {$ticket->reply_code}\n";
echo "Author: {$ticket->author->email}\n";
echo "\nPour tester, envoie un email depuis {$ticket->author->email}\n";
echo "avec le code {$ticket->reply_code} dans le sujet.\n";
