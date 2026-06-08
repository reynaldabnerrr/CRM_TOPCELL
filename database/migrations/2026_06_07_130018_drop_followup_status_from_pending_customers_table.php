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
            $table->dropColumn([
                'followup_h1_status',
                'followup_h7_status',
                'followup_h1month_status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_customers', function (Blueprint $table) {
            $table->enum('followup_h1_status', ['pending', 'done', 'skipped'])->default('pending')->after('followup_h1_date');
            $table->enum('followup_h7_status', ['pending', 'done', 'skipped'])->default('pending')->after('followup_h7_date');
            $table->enum('followup_h1month_status', ['pending', 'done', 'skipped'])->default('pending')->after('followup_h1month_date');
        });
    }
};
