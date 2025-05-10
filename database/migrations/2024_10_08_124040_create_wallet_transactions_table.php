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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->nullable()->constrained('wallets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->ulidMorphs('payable');  // Polymorphic relationship, allows this table to relate to multiple models (e.g., orders, transactions)
            $table->decimal('amount', 15, 2);        // Transaction amount
            $table->string('transaction_type')->default('deposit');      // deposit, withdrawal, transfer, etc.
            $table->string('transaction_reference')->nullable(); // Reference number or ID
            $table->string('status')->default('pending'); // Transaction status: pending, completed, failed
            $table->json('bank_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
