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
        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('cascade');
            $table->foreignId('package_category_id')->nullable()->constrained('package_categories')->onDelete('cascade');
            $table->foreignId('package_promotion_id')->nullable()->constrained('package_promotions')->onDelete('cascade');
            $table->integer('quantity');
            $table->unsignedInteger('price');
            $table->boolean('offer_enabled')->default(false);
            $table->unsignedInteger('offer_price')->nullable();
            $table->date('offer_start')->nullable();
            $table->date('offer_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_items');
    }
};
