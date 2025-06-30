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
        Schema::create('review_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relación con la review reportada
            $table->foreignUuid('review_id')
                  ->constrained('customer_reviews')
                  ->cascadeOnDelete();
            
            // Usuario que reporta
            $table->foreignUuid('reported_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // Tipo de reporte
            $table->enum('reason', [
                'spam',
                'inappropriate_content',
                'fake_review',
                'offensive_language',
                'harassment',
                'misleading_information',
                'off_topic',
                'duplicate',
                'other'
            ])->index();
            
            // Descripción detallada del reporte
            $table->text('description')->nullable();
            
            // Estado del reporte
            $table->enum('status', [
                'pending',
                'under_review',
                'resolved',
                'dismissed',
                'escalated'
            ])->default('pending')->index();
            
            // Prioridad del reporte
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium');
            
            // Moderador asignado
            $table->foreignUuid('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            // Resolución del reporte
            $table->text('resolution_notes')->nullable();
            $table->enum('resolution_action', [
                'no_action',
                'warning_sent',
                'review_hidden',
                'review_deleted',
                'user_suspended',
                'user_banned'
            ])->nullable();
            
            // Timestamps de gestión
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['assigned_to', 'status']);
            $table->index(['reason', 'status']);
            
            // Evitar reportes duplicados del mismo usuario
            $table->unique(['review_id', 'reported_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_reports');
    }
};