<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('used_package_items', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['ad_id']);
        });

        // Re-add the foreign key with ON DELETE CASCADE
        Schema::table('used_package_items', function (Blueprint $table) {
            $table->foreign('ad_id')
                ->references('id')
                ->on('ads')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('used_package_items', function (Blueprint $table) {
            // Drop the cascade foreign key
            $table->dropForeign(['ad_id']);
        });

        // Revert back to NO ACTION
        Schema::table('used_package_items', function (Blueprint $table) {
            $table->foreign('ad_id')
                ->references('id')
                ->on('ads')
                ->onDelete('no action');
        });
    }
};
