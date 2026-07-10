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
        Schema::create('qontak_settings', function (Blueprint $table) {
            $table->id();
            $table->string('base_url')->default('https://service-chat.qontak.com');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('channel_integration_id')->nullable();
            $table->string('sales_template_h1')->nullable();
            $table->string('sales_template_h7')->nullable();
            $table->string('sales_template_1month')->nullable();
            $table->string('pending_template_h1')->nullable();
            $table->string('pending_template_h7')->nullable();
            $table->string('pending_template_1month')->nullable();
            $table->timestamps();
        });

        // Seed default record
        \DB::table('qontak_settings')->insert([
            'base_url' => 'https://service-chat.qontak.com',
            'access_token' => 'YVXm1w7R853kAn_BiiDOoKdMpU__uBOYtNp2EL-RpUE',
            'refresh_token' => '_F0YHtffC52B47xqjSebAq8wC7HIk8S4JXQXiL3QeiM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qontak_settings');
    }
};
