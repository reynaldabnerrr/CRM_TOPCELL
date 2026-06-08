<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, remove duplicate phone numbers and keep only latest
        DB::statement('DELETE FROM pending_customers 
                       WHERE id NOT IN (
                           SELECT * FROM (
                               SELECT MAX(id) FROM pending_customers 
                               WHERE phone_number IS NOT NULL 
                               GROUP BY phone_number
                           ) AS t
                       ) AND phone_number IS NOT NULL');

        Schema::table('pending_customers', function (Blueprint $table) {
            // Make phone_number unique and nullable
            $table->unique('phone_number');

            // Drop old columns if they exist
            if (Schema::hasColumn('pending_customers', 'next_followup_date')) {
                $table->dropColumn('next_followup_date');
            }
            if (Schema::hasColumn('pending_customers', 'followup_status')) {
                $table->dropColumn('followup_status');
            }

            // Add H+1 follow-up columns
            $table->date('followup_h1_date')->nullable()->after('entry_date');
            $table->enum('followup_h1_status', ['pending', 'done', 'skipped'])->default('pending')->after('followup_h1_date');

            // Add H+7 follow-up columns
            $table->date('followup_h7_date')->nullable()->after('followup_h1_status');
            $table->enum('followup_h7_status', ['pending', 'done', 'skipped'])->default('pending')->after('followup_h7_date');

            // Add H+1month follow-up columns
            $table->date('followup_h1month_date')->nullable()->after('followup_h7_status');
            $table->enum('followup_h1month_status', ['pending', 'done', 'skipped'])->default('pending')->after('followup_h1month_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_customers', function (Blueprint $table) {
            $table->dropUnique('pending_customers_phone_number_unique');
            $table->dropColumn([
                'followup_h1_date', 'followup_h1_status',
                'followup_h7_date', 'followup_h7_status',
                'followup_h1month_date', 'followup_h1month_status',
            ]);
            $table->date('next_followup_date')->nullable();
            $table->string('followup_status')->nullable();
        });
    }
};
