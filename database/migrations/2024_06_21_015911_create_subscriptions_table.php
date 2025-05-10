<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id(); // Polymorphic relationship to support any subscriber type
                $table->uuidMorphs('subscriber'); // Plan ID the subscription is associated with
                $table->unsignedInteger('plan_id');  // Subscription unique identifier (for payment gateways)
                $table->string('subscription_reference')->unique();  // The date and time when the trial period ends
                $table->string('payment_method');
                $table->string('status');
                $table->timestamp('trial_ends_at')->nullable();  // The date and time when the subscription starts
                $table->timestamp('starts_at')->nullable();  // The date and time when the subscription ends
                $table->timestamp('ends_at')->nullable();  // The date and time when the subscription is canceled
                $table->timestamp('cancels_at')->nullable();  // The date and time when the subscription is paused
                $table->timestamp('paused_at')->nullable();  // Payment gateway-specific information (JSON)
                $table->json('gateway_data')->nullable();  // Additional subscription metadata (JSON)
                $table->json('metadata')->nullable();
                $table->timestamps();
                // Foreign key constraint
                $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
