<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->insert([
            ['name' => 'US Dollar', 'code' => 'USD', 'exchange_rate' => 1.00],
            ['name' => 'Euro', 'code' => 'EUR', 'exchange_rate' => null],
            ['name' => 'Japanese Yen', 'code' => 'JPY', 'exchange_rate' => null],
            ['name' => 'British Pound Sterling', 'code' => 'GBP', 'exchange_rate' => null],
            ['name' => 'Australian Dollar', 'code' => 'AUD', 'exchange_rate' => null],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'exchange_rate' => null],
            ['name' => 'Swiss Franc', 'code' => 'CHF', 'exchange_rate' => null],
            ['name' => 'Chinese Yuan Renminbi', 'code' => 'CNY', 'exchange_rate' => null],
            ['name' => 'Swedish Krona', 'code' => 'SEK', 'exchange_rate' => null],
            ['name' => 'Mexican Peso', 'code' => 'MXN', 'exchange_rate' => null],
            ['name' => 'New Zealand Dollar', 'code' => 'NZD', 'exchange_rate' => null],
            ['name' => 'Singapore Dollar', 'code' => 'SGD', 'exchange_rate' => null],
            ['name' => 'Hong Kong Dollar', 'code' => 'HKD', 'exchange_rate' => null],
            ['name' => 'Norwegian Krone', 'code' => 'NOK', 'exchange_rate' => null],
            ['name' => 'South Korean Won', 'code' => 'KRW', 'exchange_rate' => null],
            ['name' => 'Turkish Lira', 'code' => 'TRY', 'exchange_rate' => null],
            ['name' => 'Russian Ruble', 'code' => 'RUB', 'exchange_rate' => null],
            ['name' => 'Indian Rupee', 'code' => 'INR', 'exchange_rate' => null],
            ['name' => 'Brazilian Real', 'code' => 'BRL', 'exchange_rate' => null],
            ['name' => 'South African Rand', 'code' => 'ZAR', 'exchange_rate' => null],
            ['name' => 'Philippine Peso', 'code' => 'PHP', 'exchange_rate' => null],
            ['name' => 'Czech Koruna', 'code' => 'CZK', 'exchange_rate' => null],
            ['name' => 'Indonesian Rupiah', 'code' => 'IDR', 'exchange_rate' => null],
            ['name' => 'Malaysian Ringgit', 'code' => 'MYR', 'exchange_rate' => null],
            ['name' => 'Hungarian Forint', 'code' => 'HUF', 'exchange_rate' => null],
        ]);
    }
}
