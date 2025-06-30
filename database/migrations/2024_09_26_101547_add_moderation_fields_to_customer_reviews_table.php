<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('customer_reviews', 'moderation_status')) {
                $table->dropColumn('moderation_status');
            }
            if (Schema::hasColumn('customer_reviews', 'moderated_at')) {
                $table->dropColumn('moderated_at');
            }
            if (Schema::hasColumn('customer_reviews', 'moderated_by')) {
                $table->dropColumn('moderated_by');
            }
            if (Schema::hasColumn('customer_reviews', 'moderation_notes')) {
                $table->dropColumn('moderation_notes');
            }
            if (Schema::hasIndex('customer_reviews', 'customer_reviews_moderation_status_created_at_index')) {
                $table->dropIndex(['moderation_status', 'created_at']);
            }
        });

        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->string('moderation_status')->default('pending');
            $table->timestamp('moderated_at')->nullable();
            $table->foreignUuid('moderated_by')->nullable()->constrained('users');
            $table->text('moderation_notes')->nullable();
            
            $table->index(['moderation_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->dropIndex(['moderation_status', 'created_at']);
            $table->dropColumn(['moderation_status', 'moderated_at', 'moderated_by', 'moderation_notes']);
        });
    }
};
