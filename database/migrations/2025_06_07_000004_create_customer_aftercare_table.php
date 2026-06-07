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
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('type', [
                'Aftercare h+1',
                'Followup h+7',
                'Followup h+1bulan'
            ])->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('done_date')->nullable();
            $table->string('status')->nullable();
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
