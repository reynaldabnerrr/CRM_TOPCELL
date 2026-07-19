<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QontakSetting;
use Illuminate\Support\Facades\Http;

$settings = QontakSetting::first();
$baseUrl = rtrim($settings->base_url, '/');
$accessToken = $settings->access_token;
$chatbotToken = $settings->chatbot_token;
$roomId = '6468612c-3695-4388-85ee-482e59f8da96';

$localFilePath = __DIR__.'/public/images/logo.png';
$uploadUrl = $baseUrl . '/api/open/v1/file_uploader';
$botUrl = $baseUrl . '/api/open/v1/messages/whatsapp/bot';

echo "=== RUNNING QONTAK PRODUCTION TEST ===\n\n";

// Test 1: Upload with access_token, Reply with access_token
echo "--- TEST 1: Upload (access_token), Reply (access_token) ---\n";
$uploadRes1 = Http::withToken($accessToken)
    ->attach('file', file_get_contents($localFilePath), 'logo_test1.png')
    ->post($uploadUrl);
echo "Upload status: " . $uploadRes1->status() . "\n";
echo "Upload body: " . $uploadRes1->body() . "\n";

if ($uploadRes1->successful()) {
    $data = $uploadRes1->json();
    $url = $data['url'] ?? $data['data']['url'] ?? null;
    if ($url) {
        $replyPayload = [
            'room_id' => $roomId,
            'type' => 'image',
            'file_url' => $url
        ];
        $replyRes1 = Http::withToken($accessToken)->post($botUrl, $replyPayload);
        echo "Reply status: " . $replyRes1->status() . "\n";
        echo "Reply body: " . $replyRes1->body() . "\n";
    }
}
echo "\n";

// Test 2: Upload with access_token, Reply with chatbot_token
echo "--- TEST 2: Upload (access_token), Reply (chatbot_token) ---\n";
if (isset($url)) {
    $replyPayload = [
        'room_id' => $roomId,
        'type' => 'image',
        'file_url' => $url
    ];
    $replyRes2 = Http::withToken($chatbotToken)->post($botUrl, $replyPayload);
    echo "Reply status: " . $replyRes2->status() . "\n";
    echo "Reply body: " . $replyRes2->body() . "\n";
}
echo "\n";

// Test 3: Upload with chatbot_token, Reply with chatbot_token
echo "--- TEST 3: Upload (chatbot_token), Reply (chatbot_token) ---\n";
$uploadRes3 = Http::withToken($chatbotToken)
    ->attach('file', file_get_contents($localFilePath), 'logo_test3.png')
    ->post($uploadUrl);
echo "Upload status: " . $uploadRes3->status() . "\n";
echo "Upload body: " . $uploadRes3->body() . "\n";

if ($uploadRes3->successful()) {
    $data = $uploadRes3->json();
    $url3 = $data['url'] ?? $data['data']['url'] ?? null;
    if ($url3) {
        $replyPayload = [
            'room_id' => $roomId,
            'type' => 'image',
            'file_url' => $url3
        ];
        $replyRes3 = Http::withToken($chatbotToken)->post($botUrl, $replyPayload);
        echo "Reply status: " . $replyRes3->status() . "\n";
        echo "Reply body: " . $replyRes3->body() . "\n";
    }
}
echo "\n";

// Test 4: Upload with chatbot_token, Reply with access_token
echo "--- TEST 4: Upload (chatbot_token), Reply (access_token) ---\n";
if (isset($url3)) {
    $replyPayload = [
        'room_id' => $roomId,
        'type' => 'image',
        'file_url' => $url3
    ];
    $replyRes4 = Http::withToken($accessToken)->post($botUrl, $replyPayload);
    echo "Reply status: " . $replyRes4->status() . "\n";
    echo "Reply body: " . $replyRes4->body() . "\n";
}
echo "\n";
