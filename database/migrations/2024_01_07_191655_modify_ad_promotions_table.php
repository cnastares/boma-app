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
            $table->unsignedInteger('price')->nullable()->change();
            $table->foreignId('order_package_item_id')->nullable()->constrained('order_package_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ad_promotions', function (Blueprint $table) {
            //
        });
    }
};
