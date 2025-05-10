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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID as primary key
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_number');
            $table->string('linked_order_number')->nullable();
            $table->date('order_date');
            $table->double('total_amount');
            $table->double('discount_amount')->default(0);
            $table->double('subtotal_amount');
            $table->string('payment_method');
            $table->string('order_status');
            $table->string('payment_status');
            $table->string('transaction_id')->nullable();
            $table->string('shipping_tracking_number')->nullable();
            $table->string('shipping_carrier')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone_number')->nullable();
            $table->string('shipping_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
