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
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('slug', 60)->unique();
            $table->longText('description')->nullable();
            $table->integer('order')->default(0);
            $table->string('icon')->nullable(); // Optional field for SVG icon
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreignId('parent_id')->nullable()->constrained('post_categories')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_categories');
    }
};
