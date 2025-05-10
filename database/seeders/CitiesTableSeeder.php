<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFile = database_path('sql/cities.sql');
        if (file_exists($sqlFile)) {
            $sql = File::get($sqlFile);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $stmt) {
                DB::statement($stmt);
            }

            $this->command->info('Cities SQL file imported successfully.');
        } else {
            $this->command->error('SQL file does not exist.');
        }
    }
}
