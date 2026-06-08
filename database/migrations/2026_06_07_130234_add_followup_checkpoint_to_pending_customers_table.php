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
            // Add checkpoint/last follow-up date for each type
            $table->date('followup_h1_last_date')->nullable()->after('followup_h1_date');
            $table->date('followup_h7_last_date')->nullable()->after('followup_h7_date');
            $table->date('followup_h1month_last_date')->nullable()->after('followup_h1month_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_customers', function (Blueprint $table) {
            $table->dropColumn([
                'followup_h1_last_date',
                'followup_h7_last_date',
                'followup_h1month_last_date',
            ]);
        });
    }
};
