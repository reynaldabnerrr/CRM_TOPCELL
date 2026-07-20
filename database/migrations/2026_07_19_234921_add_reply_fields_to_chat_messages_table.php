<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('reply_to_message_id')->nullable()->after('message_content');
            $table->text('reply_to_message_content')->nullable()->after('reply_to_message_id');
            $table->string('reply_to_message_sender_name')->nullable()->after('reply_to_message_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['reply_to_message_id', 'reply_to_message_content', 'reply_to_message_sender_name']);
        });
    }
};
