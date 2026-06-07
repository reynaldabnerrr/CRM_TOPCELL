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
        // All followup tracking fields already added in create_sales_table migration
        // This migration is a placeholder for future modifications
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'followup_h1_date',
                'followup_h1_status',
                'followup_h7_date',
                'followup_h7_status',
                'followup_1month_date',
                'followup_1month_status',
                'last_followup_at'
            ]);
        });
    }
};
