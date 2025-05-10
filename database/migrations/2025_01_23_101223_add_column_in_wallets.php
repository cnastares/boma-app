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
        Schema::table('wallets', function (Blueprint $table) {
            $table->unsignedBigInteger('points')->default(0)->after('balance'); // Wallet balance
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('points')->default(0)->after('amount'); // Wallet balance
            $table->boolean('is_added')->default(1)->after('points'); // Wallet balance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['points']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['points', 'is_added']);
        });
    }
};
