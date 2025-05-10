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
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('name', 60);
            $table->string('email', 60);
            $table->text('comment');
            $table->ipAddress()->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['pending', 'active', 'hidden'])->default('pending');
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('blog_posts')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
