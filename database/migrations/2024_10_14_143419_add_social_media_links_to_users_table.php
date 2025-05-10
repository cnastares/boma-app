<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adding social media link columns
            $table->string('facebook_link')->nullable()->after('email'); // Place the column after the 'email' column
            $table->string('twitter_link')->nullable()->after('facebook_link');
            $table->string('instagram_link')->nullable()->after('twitter_link');
            $table->string('linkedin_link')->nullable()->after('instagram_link');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping social media link columns
            $table->dropColumn(['facebook_link', 'twitter_link', 'instagram_link', 'linkedin_link']);
        });
    }
};

