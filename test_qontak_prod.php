<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QontakSetting;
use Illuminate\Support\Facades\Http;

$settings = QontakSetting::first();
$baseUrl = rtrim($settings->base_url, '/');
$chatbotToken = $settings->chatbot_token;
$roomId = '6468612c-3695-4388-85ee-482e59f8da96';

$localFilePath = __DIR__.'/public/images/logo.png';
$botUrl = $baseUrl . '/api/open/v1/messages/whatsapp/bot';

echo "=== RUNNING QONTAK MULTIPART BOT TEST ===\n\n";

$response = Http::withToken($chatbotToken)
    ->attach('file', file_get_contents($localFilePath), 'logo.png')
    ->post($botUrl, [
        'room_id' => $roomId,
        'type' => 'image'
    ]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
