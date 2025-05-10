<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = is_vehicle_rental_active() ? 'cardeer.com' : 'adfox.com';

        $users = [
            ['name' => 'Taylor', 'email' => "admin@{$domain}", 'is_admin' => true],
            ['name' => 'Emily Johnson', 'email' => "user@{$domain}"],
            ['name' => 'Michael Smith', 'email' => 'michael.smith@example.com'],
            ['name' => 'Emma Williams', 'email' => 'emma.williams@example.com'],
            ['name' => 'David Jones', 'email' => 'david.jones@example.com'],
            ['name' => 'Olivia Brown', 'email' => 'olivia.brown@example.com'],
            ['name' => 'James Davis', 'email' => 'james.davis@example.com'],
            ['name' => 'Ava Miller', 'email' => 'ava.miller@example.com'],
            ['name' => 'William Wilson', 'email' => 'william.wilson@example.com'],
            ['name' => 'Sophia Moore', 'email' => 'sophia.moore@example.com'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),  // It's recommended to give each user a unique password or prompt them to set one on first login.
                'is_admin' => $user['is_admin'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
    }
}
