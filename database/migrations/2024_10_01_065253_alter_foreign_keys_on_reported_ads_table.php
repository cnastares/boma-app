<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForeignKeysOnReportedAdsTable extends Migration
{
    public function up(): void
    {
        Schema::table('reported_ads', function (Blueprint $table) {
            // Drop the existing foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ad_id']);

            // Add new foreign keys with onDelete('cascade')
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('ad_id')
                ->references('id')
                ->on('ads')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reported_ads', function (Blueprint $table) {
            // Drop the new foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ad_id']);

            // Revert to the old foreign keys with onDelete('no action')
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action');

            $table->foreign('ad_id')
                ->references('id')
                ->on('ads')
                ->onDelete('no action');
        });
    }
}
