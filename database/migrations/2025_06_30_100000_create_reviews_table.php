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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->foreignUuid('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('reviewed_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reviewer_type', ['client', 'provider']);
            $table->tinyInteger('rating')->unsigned()->check('rating >= 1 AND rating <= 5');
            $table->text('comment');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['ad_id', 'status']);
            $table->index(['reviewed_id', 'status']);
            $table->index(['reviewer_type', 'status']);
            
            // Prevenir reseñas duplicadas del mismo usuario para el mismo anuncio
            $table->unique(['ad_id', 'reviewer_id', 'reviewer_type'], 'unique_review_per_user_ad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};