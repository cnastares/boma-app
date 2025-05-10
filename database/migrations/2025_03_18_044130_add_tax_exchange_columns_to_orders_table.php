<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->double('tax_amount')->after('subtotal_amount');
            $table->double('converted_amount')->after('tax_amount');
            $table->double('exchange_rate')->after('converted_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tax_amount','converted_amount', 'exchange_rate']);
        });
    }
};
