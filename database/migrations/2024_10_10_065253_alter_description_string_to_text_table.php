<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Modify the 'description' column to type 'text'
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    public function down()
    {
        // Revert the 'description' column back to type 'string'
        Schema::table('categories', function (Blueprint $table) {
            $table->string('description', 255)->change(); // 255 is the default size for 'string'
        });
    }
};

