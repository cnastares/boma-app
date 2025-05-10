<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Adding social media link columns
            $table->unsignedTinyInteger('rating')->nullable()->after('detail')->comment('Rating out of 5');// Place the column after the 'detail' column
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Dropping social media link columns
            $table->dropColumn('rating');
        });
    }
};

