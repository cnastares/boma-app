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
        Schema::create('webhook_packages', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id', 160)->unique();
            $table->longText('data');
            $table->string('payment_method', 60);
            $table->enum('status', ['succeeded', 'failed', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_packages');
    }
};
