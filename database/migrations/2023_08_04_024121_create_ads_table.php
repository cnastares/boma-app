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
        Schema::create('ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug', 160)->index('slug');
            $table->unsignedInteger('price')->nullable();
            $table->dateTime('posted_date')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->enum('for_sale_by', ['owner', 'business'])->default('owner');
            $table->enum('type', ['offer', 'find'])->default('offer');
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('website_url')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_display_name')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('tags')->nullable();
            $table->boolean('display_phone')->default(0);
            $table->string('phone_number')->nullable();
            $table->string('video_link')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('price_type_id')->default(1)->constrained('price_types');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('condition_id')->nullable()->constrained('ad_conditions');
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
