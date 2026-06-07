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
        Schema::table('sales', function (Blueprint $table) {
            // Kolom untuk tracking followup dates
            $table->date('followup_h1_date')->nullable()->comment('Aftercare h+1');
            $table->date('followup_h7_date')->nullable()->comment('Followup h+7');
            $table->date('followup_1month_date')->nullable()->comment('Followup h+1 bulan');
            
            // Status untuk setiap followup (done/pending)
            $table->string('followup_h1_status')->default('pending')->comment('pending, done, skipped');
            $table->string('followup_h7_status')->default('pending')->comment('pending, done, skipped');
            $table->string('followup_1month_status')->default('pending')->comment('pending, done, skipped');
            
            // Catatan followup
            $table->text('followup_h1_notes')->nullable();
            $table->text('followup_h7_notes')->nullable();
            $table->text('followup_1month_notes')->nullable();
            
            // Last activity date untuk tracking
            $table->timestamp('last_followup_at')->nullable();
            
            // Indexes untuk query cepat
            $table->index('customer_name');
            $table->index('phone_number');
            $table->index('invoice_date');
            $table->index('followup_h1_status');
            $table->index('followup_h7_status');
            $table->index('followup_1month_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['followup_h1_status']);
            $table->dropIndex(['followup_h7_status']);
            $table->dropIndex(['followup_1month_status']);
            $table->dropIndex(['customer_name']);
            $table->dropIndex(['phone_number']);
            $table->dropIndex(['invoice_date']);
            
            $table->dropColumn([
                'followup_h1_date',
                'followup_h7_date',
                'followup_1month_date',
                'followup_h1_status',
                'followup_h7_status',
                'followup_1month_status',
                'followup_h1_notes',
                'followup_h7_notes',
                'followup_1month_notes',
                'last_followup_at',
            ]);
        });
    }
};
