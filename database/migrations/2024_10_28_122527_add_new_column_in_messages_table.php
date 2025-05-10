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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_negotiable_conversation')->default(0)->after('is_read');
            $table->boolean('is_accept_offer')->default(0)->after('is_negotiable_conversation');
            $table->foreignUuid('ad_id')->nullable()->after('is_accept_offer')->constrained('ads')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['ad_id']); // Drops the foreign key
            $table->dropColumn(['is_negotiable_conversation', 'ad_id', 'is_accept_offer']);
        });
    }
};
