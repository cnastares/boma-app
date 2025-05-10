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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('disable_condition')->default(false);
            $table->boolean('disable_price_type')->default(false);
            $table->boolean('customize_price_type')->default(false);
            $table->json('price_types')->nullable();
            $table->boolean('has_price_suffix')->default(false);
            $table->json('suffix_field_options')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('disable_condition');
            $table->dropColumn('disable_price_type');
            $table->dropColumn('customize_price_type');
            $table->dropColumn('price_types');
            $table->dropColumn('has_price_suffix');
            $table->dropColumn('suffix_field_options');
        });
    }
};
