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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_payment_id');
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['completed', 'refunded', 'failed']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('ad_promotion_id')->constrained('ad_promotions')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
