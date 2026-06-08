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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->index();
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->string('vendor')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('unit')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('tax', 15, 2)->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('total_revenue', 15, 2)->nullable();
            $table->decimal('profit', 15, 2)->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('invoice_number')->references('invoice_number')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
