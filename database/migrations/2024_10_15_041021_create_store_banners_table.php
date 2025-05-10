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
        Schema::create('store_banners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->text('alternative_text')->nullable();
            $table->string('link')->nullable();
            $table->integer('order')->default(0);
            $table->bigInteger('views')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
