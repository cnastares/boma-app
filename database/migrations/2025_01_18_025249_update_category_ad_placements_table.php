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
        Schema::table('category_ad_placements', function (Blueprint $table) {
            $table->text('value')->nullable()->change(); // Make value nullable
            $table->string('ad_type')->nullable()->after('position'); // Add new ad_type field
            $table->json('images')->nullable()->after('ad_type');     // Add new images field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_ad_placements', function (Blueprint $table) {
            $table->text('value')->nullable(false)->change(); // Revert value to non-nullable
            $table->dropColumn('ad_type');  // Remove ad_type field
            $table->dropColumn('images');   // Remove images field
        });
    }
};
