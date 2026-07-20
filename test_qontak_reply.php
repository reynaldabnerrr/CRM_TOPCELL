<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QontakSetting;
use Illuminate\Support\Facades\Http;

$settings = QontakSetting::first();
$baseUrl = rtrim($settings->base_url, '/');
$accessToken = $settings->chatbot_token ?: $settings->access_token;
$roomId = '6468612c-3695-4388-85ee-482e59f8da96';
$url = $baseUrl . '/api/open/v1/messages/whatsapp/bot';

// Fetch recent message from database for this room
$lastMessage = App\Models\ChatMessage::where('chat_id', function($query) use ($roomId) {
    $query->select('id')->from('chats')->where('room_id', $roomId);
})->whereNotNull('message_id')->orderBy('id', 'desc')->first();

if (!$lastMessage) {
    echo "No message found with message_id in database.\n";
    exit;
}

echo "Testing reply with Target Message ID: {$lastMessage->message_id}\n\n";

// Test 1: context.message_id (Qontak UUID)
echo "--- TEST 1: context.message_id (UUID) ---\n";
$payload1 = [
    'room_id' => $roomId,
    'type' => 'text',
    'text' => 'Tes reply Test 1 (context.message_id)',
    'context' => [
        'message_id' => $lastMessage->message_id
    ]
];
$res1 = Http::withToken($accessToken)->post($url, $payload1);
echo "Status: " . $res1->status() . " -> " . $res1->body() . "\n\n";

// Test 2: reply_message_id
echo "--- TEST 2: reply_message_id ---\n";
$payload2 = [
    'room_id' => $roomId,
    'type' => 'text',
    'text' => 'Tes reply Test 2 (reply_message_id)',
    'reply_message_id' => $lastMessage->message_id
];
$res2 = Http::withToken($accessToken)->post($url, $payload2);
echo "Status: " . $res2->status() . " -> " . $res2->body() . "\n\n";

// Test 3: reply.message_id
echo "--- TEST 3: reply.message_id ---\n";
$payload3 = [
    'room_id' => $roomId,
    'type' => 'text',
    'text' => 'Tes reply Test 3 (reply.message_id)',
    'reply' => [
        'message_id' => $lastMessage->message_id
    ]
];
$res3 = Http::withToken($accessToken)->post($url, $payload3);
echo "Status: " . $res3->status() . " -> " . $res3->body() . "\n\n";
