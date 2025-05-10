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
        Schema::create('contact_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('viewer_name')->nullable();
            $table->string('viewer_phone')->nullable();
            $table->string('viewer_email')->nullable();
            $table->foreignUuid('ad_id')->constrained()->onDelete('cascade');
            $table->decimal('ad_price', 10, 2);
            $table->string('ad_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_analytics');
    }
};
