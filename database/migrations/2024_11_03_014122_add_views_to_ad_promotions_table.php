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
        Schema::table('ad_promotions', function (Blueprint $table) {
            $table->bigInteger('views')->default(0);
            $table->bigInteger('clicks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ad_promotions', function (Blueprint $table) {
            $table->dropColumn('views');
            $table->dropColumn('clicks');
        });
    }
};
