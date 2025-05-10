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
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            
            // Re-add the foreign key with CASCADE on update and delete
            $table->foreign('post_id')
                  ->references('id')
                  ->on('blog_posts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        Schema::table('blog_seo', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['post_id']);
            
            // Re-add the foreign key with CASCADE on update and delete
            $table->foreign('post_id')
                  ->references('id')
                  ->on('blog_posts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('title', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            
            // Re-add the foreign key with CASCADE on update and delete
            $table->foreign('post_id')
                  ->references('id')
                  ->on('blog_posts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        Schema::table('blog_seo', function (Blueprint $table) {
            // Drop the CASCADE foreign key
            $table->dropForeign(['post_id']);
            
            // Re-add the original foreign key with NO ACTION
            $table->foreign('post_id')
                  ->references('id')
                  ->on('blog_posts')
                  ->onUpdate('no action')
                  ->onDelete('no action');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('title', 255)->unique()->change();
        });
    }
};
