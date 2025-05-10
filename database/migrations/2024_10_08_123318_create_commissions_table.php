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
        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary key as UUID
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // Foreign key to users, cascade on delete
            // Polymorphic relationship using ulidMorphs (renamed to payable for better context)
            $table->ulidMorphs('payable');  // Polymorphic relationship, allows this table to relate to multiple models (e.g., orders, transactions)
            $table->decimal('amount', 15, 2);  // Total amount for the transaction
            $table->decimal('commission_rate', 5, 2);  // Commission rate as a percentage (e.g., 10.00 for 10%)
            $table->string('commission_type', 20);  // Type of commission ('fixed', 'percentage', etc.)
            $table->decimal('commission_amount', 15, 2);  // Calculated commission amount based on amount and commission rate
            $table->string('status')->default('pending');  // Commission status ('pending', 'paid', etc.)   
            $table->timestamps();  // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
