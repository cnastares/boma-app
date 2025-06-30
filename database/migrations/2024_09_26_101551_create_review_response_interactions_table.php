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
        Schema::create('review_response_interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relación con la respuesta
            $table->foreignUuid('response_id')
                  ->constrained('review_responses')
                  ->cascadeOnDelete();
            
            // Usuario que interactúa
            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // Tipo de interacción
            $table->enum('interaction_type', [
                'helpful',
                'not_helpful',
                'report',
                'save',
                'share'
            ])->index();
            
            // Datos específicos de la interacción
            $table->json('interaction_data')->nullable()->comment('Datos específicos como motivo de reporte, etc.');
            
            // Metadatos de tracking
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['response_id', 'interaction_type']);
            $table->index(['user_id', 'interaction_type']);
            $table->index(['interaction_type', 'created_at']);
            
            // Constraint: Un usuario solo puede tener una interacción de cada tipo por respuesta
            $table->unique(['response_id', 'user_id', 'interaction_type'], 'unique_user_interaction_per_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_response_interactions');
    }
};