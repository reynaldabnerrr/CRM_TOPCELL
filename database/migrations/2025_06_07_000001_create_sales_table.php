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
            $table->date('invoice_date');
            $table->string('unit_name')->nullable();
            $table->string('department')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('customer_name');
            $table->string('phone_number')->nullable();
            $table->string('sales_person')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->default('Lunas'); // Lunas, Belum Lunas, etc
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
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
