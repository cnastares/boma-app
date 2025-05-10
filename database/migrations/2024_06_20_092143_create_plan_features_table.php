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
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id')->unsigned();
            $table->text('name')->nullable();
            $table->text('type');
            $table->longText('description')->nullable();
            $table->integer('value');
            $table->smallInteger('resettable_period')->unsigned()->nullable();
            $table->string('resettable_interval')->nullable();
            $table->mediumInteger('sort_order')->unsigned()->default(0);
            $table->softDeletes();

            // Indexes
            $table->foreignId('promotion_id')->nullable()->constrained()->onDelete('set null');
            $table->foreign('plan_id')->references('id')->on('plans')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
