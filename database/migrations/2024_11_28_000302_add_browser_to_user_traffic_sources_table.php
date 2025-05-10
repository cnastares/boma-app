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
        Schema::table('user_traffic_sources', function (Blueprint $table) {
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_traffic_sources', function (Blueprint $table) {
            $table->dropColumn('browser');
            $table->dropColumn('os');
            $table->dropColumn('device_name');
            $table->dropColumn('device_type');
        });
    }
};
