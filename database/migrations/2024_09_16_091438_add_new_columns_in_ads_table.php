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
        Schema::table('ads', function (Blueprint $table) {

            $table->after('status', function ($table) {
                // SKU field (unique string identifier)
                $table->string('sku', 20)->unique()->nullable();
                $table->boolean('enable_cash_on_delivery')->default(0);
                // Foreign key constraints
                $table->foreignUuid('return_policy_id')->nullable()->constrained('return_policies')->cascadeOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
            ]);

            $table->dropForeign('return_policy_id');
        });
    }
};
