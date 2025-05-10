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
            $table->string('whatsapp_number')->nullable()->after('phone_number'); // Add WhatsApp number field
            $table->boolean('display_whatsapp')->default(false)->after('whatsapp_number'); // Add same number boolean field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'display_whatsapp']); // Remove added fields
        });
    }
};
