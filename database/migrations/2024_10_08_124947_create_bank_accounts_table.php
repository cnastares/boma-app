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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('bank_id')->nullable()->constrained('banks')->cascadeOnDelete();
            $table->string('account_number');
            $table->string('ifsc_code')->nullable();               // Bank account number
            $table->string('account_holder_name');          // Account holder's name
            $table->string('status')->default('active'); 
            $table->boolean('is_default')->default(0);   // Status of the bank account (active/inactive)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
