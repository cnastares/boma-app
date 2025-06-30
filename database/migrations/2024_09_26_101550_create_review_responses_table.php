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
        Schema::create('review_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relación con la review original
            $table->foreignUuid('review_id')
                  ->constrained('customer_reviews')
                  ->cascadeOnDelete();
            
            // Usuario que responde (propietario del anuncio/perfil)
            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // Contenido de la respuesta
            $table->text('response');
            
            // Estado de la respuesta
            $table->enum('status', [
                'active',
                'hidden',
                'deleted',
                'pending_moderation'
            ])->default('active')->index();
            
            // Información de moderación
            $table->enum('moderation_status', [
                'pending',
                'approved', 
                'rejected',
                'flagged'
            ])->default('approved')->index();
            
            $table->foreignUuid('moderated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_notes')->nullable();
            
            // Interacciones de la comunidad
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->unsignedInteger('reported_count')->default(0);
            
            // Metadatos
            $table->json('metadata')->nullable()->comment('Datos adicionales como ediciones, flags, etc.');
            $table->timestamp('last_edited_at')->nullable();
            $table->foreignUuid('edited_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Control de calidad automático
            $table->decimal('content_score', 3, 2)->nullable()->comment('Score de calidad del contenido (0-10)');
            $table->json('auto_flags')->nullable()->comment('Flags automáticos detectados');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para optimización
            $table->index(['review_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['moderation_status', 'created_at']);
            $table->index(['status', 'moderation_status']);
            
            // Constraint: Un usuario solo puede responder una vez por review
            $table->unique(['review_id', 'user_id'], 'unique_user_response_per_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_responses');
    }
};