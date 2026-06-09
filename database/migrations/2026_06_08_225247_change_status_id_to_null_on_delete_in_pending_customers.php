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
        Schema::table('pending_customers', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->foreign('status_id')
                  ->references('id')
                  ->on('pending_customer_statuses')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pending_customers', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->foreign('status_id')
                  ->references('id')
                  ->on('pending_customer_statuses')
                  ->cascadeOnDelete();
        });
    }
};
