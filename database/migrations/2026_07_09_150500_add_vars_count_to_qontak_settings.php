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
            $table->integer('sales_template_h1_vars')->default(3);
            $table->integer('sales_template_h7_vars')->default(3);
            $table->integer('sales_template_1month_vars')->default(3);
            $table->integer('pending_template_h1_vars')->default(2);
            $table->integer('pending_template_h7_vars')->default(2);
            $table->integer('pending_template_1month_vars')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qontak_settings', function (Blueprint $table) {
            $table->dropColumn([
                'sales_template_h1_vars',
                'sales_template_h7_vars',
                'sales_template_1month_vars',
                'pending_template_h1_vars',
                'pending_template_h7_vars',
                'pending_template_1month_vars',
            ]);
        });
    }
};
