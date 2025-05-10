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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('stripe_coupon_id')->nullable();
            $table->enum('type', ['fixed', 'percentage'])->default('fixed'); // 'fixed' for fixed amount, 'percentage' for percentage discount
            $table->decimal('discount_value', 8, 2); // Discount amount or percentage
            $table->integer('usage_limit')->nullable(); // Maximum times the coupon can be used
            $table->dateTime('expires_at')->nullable(); // Expiration date
            $table->boolean('is_active')->default(true); // Coupon status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
