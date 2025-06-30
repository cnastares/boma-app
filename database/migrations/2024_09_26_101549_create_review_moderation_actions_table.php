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
        Schema::create('review_moderation_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Review afectada
            $table->foreignUuid('review_id')
                  ->constrained('customer_reviews')
                  ->cascadeOnDelete();
            
            // Moderador que realizó la acción
            $table->foreignUuid('moderator_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // Tipo de acción realizada
            $table->enum('action_type', [
                'approved',
                'rejected',
                'flagged',
                'hidden',
                'deleted',
                'edited',
                'restored',
                'escalated',
                'auto_approved',
                'auto_rejected'
            ])->index();
            
            // Estado anterior y nuevo
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            
            // Razón de la acción
            $table->enum('reason', [
                'meets_guidelines',
                'inappropriate_content',
                'spam',
                'fake_review',
                'offensive_language',
                'harassment',
                'misleading_info',
                'duplicate_content',
                'technical_issue',
                'user_request',
                'policy_violation',
                'other'
            ])->nullable();
            
            // Notas de la acción
            $table->text('notes')->nullable();
            
            // Metadata adicional
            $table->json('metadata')->nullable()->comment('Datos adicionales como cambios específicos, scores, etc.');
            
            // Si fue una acción automática
            $table->boolean('is_automated')->default(false);
            $table->string('automation_rule')->nullable();
            
            // IP y user agent del moderador
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['review_id', 'created_at']);
            $table->index(['moderator_id', 'action_type']);
            $table->index(['action_type', 'created_at']);
            $table->index(['is_automated', 'automation_rule']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_moderation_actions');
    }
};