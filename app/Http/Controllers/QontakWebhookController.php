<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Sale;
use App\Models\PendingCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QontakWebhookController extends Controller
{
    /**
     * Handle incoming webhook requests from Qontak.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        Log::info('Qontak Webhook Received Payload:', $payload);

        // Verification step requested by Qontak
        if ($request->has('verify_info')) {
            Log::info('Qontak Webhook Verification Triggered. Info: ' . $request->input('verify_info'));
            return response()->json([
                'verify_info' => $request->input('verify_info')
            ], 200);
        }

        // Parse key details from Qontak webhook payload
        $roomId = $payload['room_id'] 
            ?? $payload['data']['room_id'] 
            ?? $payload['room']['id'] 
            ?? $payload['data']['room']['id'] 
            ?? null;

        if (!$roomId) {
            Log::warning('Qontak Webhook: room_id not found in payload.');
            return response()->json(['status' => 'error', 'message' => 'room_id not found'], 200);
        }

        $text = $payload['text'] 
            ?? $payload['data']['text'] 
            ?? $payload['data']['body'] 
            ?? $payload['data']['message']['text'] 
            ?? $payload['message']['text'] 
            ?? '';

        $messageType = 'text';
        $messageContent = $text;

        $rawType = $payload['data']['message']['type']
            ?? $payload['data']['type']
            ?? $payload['type']
            ?? null;

        if ($rawType && $rawType !== 'text') {
            $messageType = $rawType;
            $mediaUrl = $payload['data']['message']['image']['url']
                ?? $payload['data']['message']['document']['url']
                ?? $payload['data']['message']['video']['url']
                ?? $payload['data']['message']['audio']['url']
                ?? $payload['data']['message']['file']['url']
                ?? $payload['data']['message']['attachment']['url']
                ?? $payload['data']['attachment']['url']
                ?? $payload['attachment']['url']
                ?? $payload['data']['media_url']
                ?? null;

            if ($mediaUrl) {
                $messageContent = $mediaUrl;
                $text = $messageType === 'image' ? '[Gambar]' : '[' . ucfirst($messageType) . ']';
            }
        }

        $phone = $payload['phone_number'] 
            ?? $payload['room']['account_uniq_id'] 
            ?? $payload['data']['room']['account_uniq_id'] 
            ?? $payload['data']['sender']['phone_number'] 
            ?? $payload['data']['phone_number'] 
            ?? $payload['data']['customer']['phone_number'] 
            ?? $payload['sender']['phone'] 
            ?? null;

        $name = $payload['room']['name'] 
            ?? $payload['sender']['name'] 
            ?? $payload['sender_name'] 
            ?? $payload['data']['sender']['name'] 
            ?? $payload['data']['sender_name'] 
            ?? $payload['data']['customer']['name'] 
            ?? 'WhatsApp Customer';

        $messageId = $payload['message_id'] 
            ?? $payload['id'] 
            ?? $payload['data']['id'] 
            ?? $payload['data']['message']['id'] 
            ?? null;

        $eventType = $payload['event'] 
            ?? $payload['data_event'] 
            ?? $payload['webhook_event'] 
            ?? null;

        // If the event is message_interaction, check the data_event type
        if ($eventType === 'message_interaction' && isset($payload['data_event'])) {
            $eventType = $payload['data_event'];
        }

        // We only process customer messages (incoming)
        // If event type is status_message or others, we can log and return success
        if ($eventType && $eventType !== 'receive_message_from_customer') {
            Log::info('Qontak Webhook: Event ' . $eventType . ' skipped.');
            return response()->json(['status' => 'ignored'], 200);
        }

        if (!$phone) {
            Log::warning('Qontak Webhook: phone_number not found in payload.');
            return response()->json(['status' => 'error', 'message' => 'phone_number not found'], 200);
        }

        try {
            // Standardize phone number for lookup (digits only)
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

            // Attempt to resolve customer name from Sales or Pending Customers
            $existingSale = Sale::where('phone_number', 'LIKE', '%' . $cleanPhone . '%')->first();
            $existingPending = PendingCustomer::where('phone_number', 'LIKE', '%' . $cleanPhone . '%')->first();

            if ($existingSale) {
                $name = $existingSale->customer_name;
            } elseif ($existingPending) {
                $name = $existingPending->name;
            }

            // Find or create chat room
            $chat = Chat::where('room_id', $roomId)->first();

            if ($chat) {
                $chat->update([
                    'customer_name' => $name,
                    'phone_number' => $phone,
                    'last_message' => $text,
                    'last_message_time' => now(),
                    'unread_count' => $chat->unread_count + 1,
                ]);
            } else {
                $chat = Chat::create([
                    'room_id' => $roomId,
                    'customer_name' => $name,
                    'phone_number' => $phone,
                    'last_message' => $text,
                    'last_message_time' => now(),
                    'unread_count' => 1,
                ]);
            }

            // Save message log
            ChatMessage::create([
                'chat_id' => $chat->id,
                'message_id' => $messageId,
                'sender_type' => 'customer',
                'sender_name' => $name,
                'message_type' => $messageType,
                'message_content' => $messageContent,
            ]);

            Log::info("Qontak Webhook: Message from customer '{$name}' processed successfully. Room ID: {$roomId}");

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Qontak Webhook Processing Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
