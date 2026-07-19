<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\ChatMessage;
$count = ChatMessage::where('message_type', 'text')
    ->where('message_content', 'like', 'https://cdn.qontak.com%')
    ->update(['message_type' => 'image']);
echo "Fixed {$count} old image messages in database.\n";
