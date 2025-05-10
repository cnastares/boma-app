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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('ad_count')->default(0);
            $table->integer('feature_ad_count')->default(0);
            $table->integer('urgent_ad_count')->default(0);
            $table->integer('spotlight_ad_count')->default(0);
            $table->integer('website_url_count')->default(0);
            $table->decimal('price')->default('0.00');
            $table->boolean('is_admin_granted')->default(0);
            // Drop the unique constraint first
            $table->dropUnique(['subscription_reference']);

            // Modify the column to be nullable
            $table->string('subscription_reference')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('ad_count');
            $table->dropColumn('feature_ad_count');
            $table->dropColumn('urgent_ad_count');
            $table->dropColumn('spotlight_ad_count');
            $table->dropColumn('website_url_count');
            $table->string('subscription_reference')->unique()->nullable(false)->change();
        });
    }
};
