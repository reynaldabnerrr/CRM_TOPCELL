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
        Schema::create('customer_aftercare', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('phone_number');
            $table->enum('type', [
                'Aftercare h+1',
                'Followup h+7',
                'Followup h+1bulan'
            ]);
            $table->date('scheduled_date');
            $table->date('done_date')->nullable();
            $table->string('status')->default('pending'); // pending, completed, skipped
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_aftercare');
    }
};
