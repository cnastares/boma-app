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
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamp('deleted_by_buyer_at')->nullable();
            $table->timestamp('deleted_by_seller_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('ad_id')->constrained('ads')->onDelete('no action');
            $table->foreignUuid('buyer_id')->constrained('users')->onDelete('no action');
            $table->foreignUuid('seller_id')->constrained('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
