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
        Schema::create('bidirectional_review_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignUuid('responder_id')->constrained('users')->cascadeOnDelete();
            $table->text('response_text');
            $table->tinyInteger('client_rating')->unsigned()->nullable()->check('client_rating >= 1 AND client_rating <= 5');
            $table->timestamps();
            
            // Una respuesta única por reseña
            $table->unique('review_id');
            
            // Índice para búsquedas por responder
            $table->index('responder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidirectional_review_responses');
    }
};