<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'room_id',
        'customer_name',
        'phone_number',
        'last_message',
        'last_message_time',
        'unread_count',
    ];

    protected function casts(): array
    {
        return [
            'last_message_time' => 'datetime',
            'unread_count' => 'integer',
        ];
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }
}
