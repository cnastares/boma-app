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
        Schema::create('footer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_section_id')->constrained()->onDelete('cascade');
            $table->text('name');
            $table->enum('type', ['page', 'url', 'predefined']);
            $table->string('predefined_identifier')->nullable();
            $table->foreignId('page_id')->nullable()->constrained('pages');
            $table->string('url')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_items');
    }
};
