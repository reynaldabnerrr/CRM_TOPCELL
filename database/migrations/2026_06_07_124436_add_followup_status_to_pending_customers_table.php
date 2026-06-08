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
            $table->string('followup_status')->default('pending')->after('next_followup_date'); // pending, completed, skipped
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_customers', function (Blueprint $table) {
            $table->dropColumn('followup_status');
        });
    }
};
