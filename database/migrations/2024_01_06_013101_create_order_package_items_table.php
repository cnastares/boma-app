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
        Schema::create('order_package_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('activation_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->integer('purchased')->default(0);
            $table->integer('available')->default(0);
            $table->integer('used')->default(0);
            $table->integer('duration');
            $table->enum('type', ['promotion', 'ad_count']);
            $table->foreignId('order_package_id')->constrained('order_packages')->onDelete('cascade');
            $table->foreignId('package_item_id')->constrained('package_items')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_package_items');
    }
};
