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
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('payment_method', 60);
            $table->string('total_value', 20);
            $table->string('subtotal_value', 20);
            $table->string('taxes_value', 20)->default(0);
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->enum('status', ['completed', 'refunded', 'failed']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_packages');
    }
};
