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
        Schema::table('qontak_settings', function (Blueprint $table) {
            $table->json('variable_mappings')->nullable()->after('pending_template_1month_vars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qontak_settings', function (Blueprint $table) {
            $table->dropColumn('variable_mappings');
        });
    }
};
