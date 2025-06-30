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
        Schema::table('customer_reviews', function (Blueprint $table) {
            // Agregar columnas que faltan según el modelo
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->unsignedInteger('reported_count')->default(0);
            $table->json('auto_moderation_flags')->nullable();
            $table->decimal('content_score', 3, 2)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // Índices para optimizar consultas
            $table->index('reported_count');
            $table->index(['moderation_status', 'reported_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->dropIndex(['moderation_status', 'reported_count']);
            $table->dropIndex(['reported_count']);
            $table->dropColumn([
                'helpful_count',
                'not_helpful_count', 
                'reported_count',
                'auto_moderation_flags',
                'content_score',
                'approved_at',
                'rejected_at'
            ]);
        });
    }
};