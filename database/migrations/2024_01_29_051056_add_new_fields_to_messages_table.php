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
            $table->text('attachment')->nullable()->after('content');
            $table->boolean('is_audio')->default(false)->after('attachment');
            $table->boolean('seen')->default(false)->after('is_audio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
