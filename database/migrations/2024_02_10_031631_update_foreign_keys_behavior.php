<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysBehavior extends Migration
{
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['buyer_id']);
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['ad_id']);

            // Recreate foreign keys with cascadeOnDelete()
            $table->foreign('buyer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('seller_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ad_id')->references('id')->on('ads')->cascadeOnDelete();
        });

        Schema::table('messages', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropForeign(['conversation_id']);

            // Re-establish foreign key constraints with cascadeOnDelete
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('receiver_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
        });

        Schema::table('reported_ads', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ad_id']);

            // Re-establish foreign key constraints with cascadeOnDelete
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ad_id')->references('id')->on('ads')->cascadeOnDelete();
        });

        Schema::table('order_upgrades', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['user_id']);

            // Re-establish foreign key constraint with cascadeOnDelete
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('order_packages', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['user_id']);

            // Re-establish foreign key constraint with cascadeOnDelete
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('ad_promotions', function (Blueprint $table) {
            $table->dropForeign(['ad_id']);

            $table->foreign('ad_id')->references('id')->on('ads')->cascadeOnDelete();
        });


    }

    public function down()
    {
        // Add the logic to revert the foreign key changes if necessary.
        // This would involve dropping the newly added constraints and adding back the original ones.
    }
}
