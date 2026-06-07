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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date')->nullable();
            $table->string('unit_name')->nullable();
            $table->string('department')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('sales_person')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            
            // Followup Tracking Fields
            $table->date('followup_h1_date')->nullable();
            $table->string('followup_h1_status')->default('pending')->nullable();
            $table->date('followup_h7_date')->nullable();
            $table->string('followup_h7_status')->default('pending')->nullable();
            $table->date('followup_1month_date')->nullable();
            $table->string('followup_1month_status')->default('pending')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
