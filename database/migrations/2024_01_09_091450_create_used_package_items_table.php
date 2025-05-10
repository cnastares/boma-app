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
        Schema::create('used_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ad_id')->constrained('ads')->onDelete('no action');
            $table->foreignId('order_package_item_id')->constrained('order_package_items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_package_items');
    }
};
