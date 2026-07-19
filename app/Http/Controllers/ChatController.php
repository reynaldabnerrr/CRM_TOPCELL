<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\QontakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $qontakService;

    public function __construct(QontakService $qontakService)
    {
        $this->qontakService = $qontakService;
    }

    /**
     * Display the main chat dashboard.
     */
    public function index()
    {
        $chats = Chat::orderBy('last_message_time', 'desc')->get();
        return view('chats.index', compact('chats'));
    }

    /**
     * Get list of chat rooms for polling.
     */
    public function getRooms()
    {
        $chats = Chat::orderBy('last_message_time', 'desc')->get();
        return response()->json($chats);
    }

    /**
     * Get messages for a specific chat room and mark as read.
     */
    public function getMessages(Chat $chat)
    {
        // Mark room as read
        $chat->update(['unread_count' => 0]);

        $messages = $chat->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'chat' => $chat,
            'messages' => $messages
        ]);
    }

    /**
     * Send message to WhatsApp room via Qontak and save to database.
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $text = $request->input('message');

        Log::info("ChatController: Attempting to send reply to Room {$chat->room_id}");

        // Call Qontak Service to send message
        $result = $this->qontakService->sendWhatsappReply($chat->room_id, $text);

        if ($result['success']) {
            try {
                // Save the message locally
                $message = ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_type' => 'agent',
                    'sender_name' => auth()->user()->name,
                    'message_type' => 'text',
                    'message_content' => $text,
                ]);

                // Update chat room last message info
                $chat->update([
                    'last_message' => $text,
                    'last_message_time' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);

            } catch (\Exception $e) {
                Log::error('ChatController: Error saving reply message: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Pesan berhasil dikirim via Qontak, namun gagal disimpan di database lokal.',
                ], 500);
            }
        }

        // Return error message from API
        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Gagal mengirim pesan melalui Qontak.',
        ], 422);
    }
}
