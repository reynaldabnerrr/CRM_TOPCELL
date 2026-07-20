<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_id',
        'message_id',
        'sender_type',
        'sender_name',
        'message_type',
        'message_content',
        'reply_to_message_id',
        'reply_to_message_content',
        'reply_to_message_sender_name',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
}
