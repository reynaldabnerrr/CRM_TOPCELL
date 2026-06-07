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
        Schema::create('pending_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number');
            $table->enum('status', [
                'Chat masuk',
                'On proses',
                'Follow up h+1',
                'Follow up h+2',
                'Follow up h+3',
                'Closing',
                'Lost contact',
                'Budget kurang',
                'Barang kosong',
                'Tunggu kabar keluarga',
                'Perbandingan harga',
                'Sudah beli di toko lain'
            ])->default('Chat masuk');
            $table->text('notes')->nullable();
            $table->date('last_followup_date')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_customers');
    }
};
