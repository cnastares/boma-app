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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('retail')->after('user_id');
            $table->string('points')->default('0')->after('order_type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('points')->default('0')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'points']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['points']);
        });
    }
};
