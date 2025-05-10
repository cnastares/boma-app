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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Auto-generated UUID as primary key
            $table->string('name', 255); // Name field with max length 255 characters
            $table->string('type', 255)->default('warehouse');
            $table->string('house_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address'); // Address field with max length 255 characters
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('postal_code', 20); // Postal code field with max length 20 characters
            $table->decimal('latitude', 10, 7)->nullable(); // Latitude for geolocation
            $table->decimal('longitude', 10, 7)->nullable(); // Longitude for geolocation
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
