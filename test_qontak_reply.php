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

// Find a message with wamid (external_id) in recent webhooks or database
// Let's test with a real wamid
$wamid = "wamid.HBgNNjI4NTE1NjQzMTU2MxUCABEYEjY0RDYwQjU2RDlFREI2RUUyOQA=";
$uuid = "8e40a417-8a93-4b49-a969-713414e16686";

echo "Testing reply with WAMID: {$wamid}\n\n";

// Test 4: context.message_id = WAMID
echo "--- TEST 4: context.message_id = WAMID ---\n";
$payload4 = [
    'room_id' => $roomId,
    'type' => 'text',
    'text' => 'Tes reply Test 4 (context.message_id = WAMID)',
    'context' => [
        'message_id' => $wamid
    ]
];
$res4 = Http::withToken($accessToken)->post($url, $payload4);
echo "Status: " . $res4->status() . " -> " . $res4->body() . "\n\n";

// Test 5: reply_id = WAMID
echo "--- TEST 5: reply_id = WAMID ---\n";
$payload5 = [
    'room_id' => $roomId,
    'type' => 'text',
    'text' => 'Tes reply Test 5 (reply_id = WAMID)',
    'reply_id' => $wamid
];
$res5 = Http::withToken($accessToken)->post($url, $payload5);
echo "Status: " . $res5->status() . " -> " . $res5->body() . "\n\n";

// Test 6: reply.id = UUID / WAMID
echo "--- TEST 6: reply.id = WAMID ---\n";
$payload6 = [
    'room_id' => $roomId,
    'type' => 'text',
    'text' => 'Tes reply Test 6 (reply.id = WAMID)',
    'reply' => [
        'id' => $wamid
    ]
];
$res6 = Http::withToken($accessToken)->post($url, $payload6);
echo "Status: " . $res6->status() . " -> " . $res6->body() . "\n\n";
