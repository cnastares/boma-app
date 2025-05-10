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
        Schema::create('ad_types', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();

            // Ad Configuration
            $table->boolean('enable_title')->default(true);
            $table->boolean('enable_description')->default(true);
            $table->boolean('enable_price')->default(true);
            $table->boolean('enable_offer_price')->default(true);
            $table->boolean('disable_price_type')->default(false);
            $table->boolean('customize_price_type')->default(false);
            $table->json('price_types')->nullable();
            $table->boolean('has_price_suffix')->default(false);
            $table->json('suffix_field_options')->nullable();

            // Location Settings
            $table->boolean('disable_location')->default(false);
            $table->boolean('default_location')->default(false);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            // Additional Options
            $table->boolean('enable_tags')->default(false);
            $table->boolean('allowed_comment')->default(false);
            $table->boolean('enable_for_sale_by')->default(true);
            $table->boolean('enable_sell_item_with_option')->default(false);
            $table->boolean('disable_condition')->default(false);

            // Filter Configuration
            $table->boolean('enable_filters')->default(false);
            $table->json('filter_options')->nullable();

            // Marketplace Configuration
            $table->string('marketplace')->default('classified');
            $table->json('marketplace_options')->nullable();

            // Image Configuration
            $table->boolean('allowed_upload_image')->default(false);
            $table->json('upload_image_options')->nullable();
            $table->boolean('allow_youtube_video')->default(false);

            $table->boolean('is_default')->default(false);
            $table->timestamps();
            // Add soft deletes if needed
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_types');
    }
};
