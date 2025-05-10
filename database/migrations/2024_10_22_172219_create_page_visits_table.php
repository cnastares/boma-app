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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->uuidMorphs('visitable'); // for polymorphic relation, using UUID
            $table->integer('time_spent_in_secs'); // time spent in seconds
            $table->string('ip_address')->nullable(); // Track IP address
            $table->string('device')->nullable(); // Track device (mobile/desktop)
            $table->string('browser')->nullable(); // Track browser information
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
