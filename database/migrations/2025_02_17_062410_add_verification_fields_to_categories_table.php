<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('enable_age_verify')->default(false);
            $table->integer('age_value')->nullable();
            $table->boolean('enable_identity_verify')->default(false);
            $table->boolean('enable_manual_approval')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'enable_age_verify',
                'age_value',
                'enable_identity_verify',
                'enable_manual_approval',
            ]);
        });
    }
};

