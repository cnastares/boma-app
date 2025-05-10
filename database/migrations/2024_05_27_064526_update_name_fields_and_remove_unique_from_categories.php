<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNameFieldsAndRemoveUniqueFromCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['name']); // Remove the unique constraint
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('name')->change(); // Change the column type to text
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->text('name')->change(); // Change the column type to text
        });

        Schema::table('field_groups', function (Blueprint $table) {
            $table->text('name')->change(); // Change the column type to text
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name', 60)->unique()->change(); // Revert the column type and add unique constraint back
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->string('name', 255)->change(); // Revert the column type
        });

        Schema::table('field_groups', function (Blueprint $table) {
            $table->string('name', 255)->change(); // Revert the column type
        });
    }
}
