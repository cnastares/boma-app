<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTrafficSourcesTable extends Migration
{
    public function up()
    {
        Schema::create('user_traffic_sources', function (Blueprint $table) {
            $table->id();
            $table->string('utm_source')->nullable();   // Source of the UTM parameter (e.g., Google, Facebook)
            $table->string('utm_medium')->nullable();   // Medium of the UTM parameter (e.g., organic, cpc)
            $table->string('utm_campaign')->nullable(); // Campaign name (e.g., Summer Sale)
            $table->string('utm_term')->nullable();     // Keywords for paid search (optional)
            $table->string('utm_content')->nullable();  // Content for A/B testing (optional)
            $table->string('traffic_source')->nullable(); // Source of traffic (e.g., google_search)
            $table->string('referrer_url')->nullable(); // URL from where the user came
            $table->string('full_url')->nullable(); // Store the full URL visited
            $table->ipAddress('visitor_ip')->nullable(); // IP address of the visitor for analysis
            $table->string('user_agent')->nullable();   // User agent string for identifying device
            $table->uuidMorphs('trackable'); // Polymorphic relation, can be a User, Product, etc.
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->json('location_data')->nullable(); // Location Data from ip address
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_traffic_sources');
    }
}
