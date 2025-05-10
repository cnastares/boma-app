<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_free')->default(false); // Automated alerts support
            // Common customizable features for any plan
            $table->tinyInteger('images_limit')->default(2); // Number of images allowed
            $table->boolean('video_posting')->default(false); // Enable video posting
            $table->string('ads_level')->nullable(); // Enable ads
            $table->integer('banner_count')->default(0); // Banner support
            $table->boolean('enable_user_profile_description')->default(true); // User profile description
            $table->boolean('enable_social_media_links')->default(false); // User profile description
            $table->boolean('enable_location')->default(false); // User profile description
            $table->boolean('rating')->default(1); // Rating system
            $table->string('filter_options_level')->nullable(); // Filter options support
            $table->string('seo_tools_level')->nullable(); // SEO support
            $table->string('utm_parameters_level')->nullable(); // UTM parameters support
            $table->boolean('number_of_visits')->default(true); // Visit tracking
            $table->string('traffic_source')->nullable(); // Traffic source tracking
            $table->boolean('average_view_time')->default(false); // Average view time tracking
            $table->boolean('number_of_favorites')->default(true); // Favorites tracking
            $table->string('contact_conversion_rate_level')->nullable(); // Contact conversion rate tracking
            $table->boolean('clicks_on_link')->default(false); // Click tracking
            $table->string('demographic_analysis_level')->nullable(); // Demographic analysis
            $table->string('product_engagement_level')->nullable(); // Product engagement tracking
            $table->boolean('product_performance_analysis')->default(false); // Product performance analysis
            $table->boolean('boost_analysis')->default(false); // Boost analysis
            $table->string('custom_reports_level')->nullable(); // Custom reports support
            $table->boolean('automated_alerts')->default(false); // Automated alerts support
            $table->integer('chat_limit')->default(20); // Limit on chat interactions
            $table->boolean('automated_messages')->default(false); // Automated alerts support
            $table->boolean('automated_email_marketing')->default(false); // Automated alerts support
            $table->integer('ad_count')->default(0);
            $table->integer('feature_ad_count')->default(0);
            $table->integer('urgent_ad_count')->default(0);
            $table->integer('spotlight_ad_count')->default(0);
            $table->integer('website_url_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
