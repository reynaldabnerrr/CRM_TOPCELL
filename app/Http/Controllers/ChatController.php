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
        $statuses = \App\Models\PendingCustomerStatus::orderBy('name')->get();
        return view('chats.index', compact('chats', 'statuses'));
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
            'message' => 'required_without:file|nullable|string',
            'file' => 'required_without:message|nullable|file|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $messageType = 'text';
        $content = '';

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('attachments', 'public');
            $filename = basename($path);
            // Use dedicated Laravel route instead of storage symlink URL
            $content = url('/attachments/' . $filename);
            $messageType = 'image';
        } else {
            $content = $request->input('message');
        }

        Log::info("ChatController: Attempting to send reply to Room {$chat->room_id} (type: {$messageType})");

        // Call Qontak Service to send message
        $result = $this->qontakService->sendWhatsappReply($chat->room_id, $content, $messageType);

        if ($result['success']) {
            try {
                // Save the message locally
                $message = ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_type' => 'agent',
                    'sender_name' => auth()->user()->name,
                    'message_type' => $messageType,
                    'message_content' => $content,
                ]);

                // Update chat room last message info
                $chat->update([
                    'last_message' => $messageType === 'image' ? '[Gambar]' : $content,
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

    public function addToPendingCustomers(Request $request, Chat $chat)
    {
        $request->validate([
            'create_new_status' => 'required|boolean',
            'status_id' => 'required_without:new_status_name|nullable|exists:pending_customer_statuses,id',
            'new_status_name' => 'required_if:create_new_status,true|nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            // Check if customer already exists in pending customers by phone number
            $exists = \App\Models\PendingCustomer::where('phone_number', $chat->phone_number)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'error' => 'Customer ini sudah terdaftar sebagai Calon Customer.'
                ], 422);
            }

            if ($request->boolean('create_new_status')) {
                // Check if status name already exists
                $status = \App\Models\PendingCustomerStatus::where('name', $request->new_status_name)->first();
                if (!$status) {
                    $status = \App\Models\PendingCustomerStatus::create([
                        'name' => $request->new_status_name,
                    ]);
                }
                $statusId = $status->id;
            } else {
                $statusId = $request->status_id;
            }

            $entryDate = now();

            \App\Models\PendingCustomer::create([
                'name' => $chat->customer_name,
                'phone_number' => $chat->phone_number,
                'entry_date' => $entryDate->toDateString(),
                'status_id' => $statusId,
                'notes' => $request->notes ?? 'Ditambahkan langsung dari live chat WhatsApp.',
                'followup_h1_date' => $entryDate->clone()->addDay()->toDateString(),
                'followup_h7_date' => $entryDate->clone()->addDays(7)->toDateString(),
                'followup_h1month_date' => $entryDate->clone()->addMonth(1)->toDateString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambahkan customer ke daftar Calon Customer!',
                'statuses' => $request->boolean('create_new_status') 
                    ? \App\Models\PendingCustomerStatus::orderBy('name')->get() 
                    : null
            ]);
        } catch (\Exception $e) {
            Log::error('ChatController: Error adding to pending customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat menambahkan data.'
            ], 500);
        }
    }

    /**
     * Delete the chat room, its messages, and any locally stored image files.
     */
    public function destroy(Chat $chat)
    {
        try {
            // First, delete locally stored attachment files from storage
            $imageMessages = $chat->messages()
                ->where('message_type', 'image')
                ->get(['message_content']);

            foreach ($imageMessages as $msg) {
                $url = $msg->message_content;
                // Only delete files hosted on our own server (not external CDN URLs like qontak.com)
                if ($url && str_contains($url, '/storage/attachments/')) {
                    // Extract relative path from the URL: storage/attachments/filename.jpg
                    $relativePath = 'attachments/' . basename(parse_url($url, PHP_URL_PATH));
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
                        Log::info("ChatController: Deleted attachment file: {$relativePath}");
                    }
                }
            }

            // Delete all associated messages
            $chat->messages()->delete();
            
            // Delete the chat room
            $chat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat beserta file lampiran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('ChatController: Error deleting chat room: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus chat.'
            ], 500);
        }
    }
}
