<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->unsignedBigInteger('main_category_id')->nullable()->after('id'); // New field
            $table->unsignedBigInteger('category_id')->nullable()->change(); // Make existing category_id nullable

            // If categories table exists, add foreign key (optional)
            $table->foreign('main_category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropForeign(['main_category_id']); // Remove foreign key
            $table->dropColumn('main_category_id'); // Drop new column
            $table->unsignedBigInteger('category_id')->nullable(false)->change(); // Revert category_id back to required
        });
    }
};
