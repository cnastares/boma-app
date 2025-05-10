<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ad_types', function (Blueprint $table) {
            $table->json('location_details')->nullable()->after('id'); // Add JSON column
        });
    }

    public function down()
    {
        Schema::table('ad_types', function (Blueprint $table) {
            $table->dropColumn('location_details'); // Remove column on rollback
        });
    }
};
