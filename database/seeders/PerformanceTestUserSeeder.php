<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PerformanceTestUserSeeder extends Seeder
{
    public function run()
    {
        // Disable events to speed up
        User::flushEventListeners();

        $users = [];
        $password = Hash::make('password');
        $now = now();

        for ($i = 1; $i <= 55; $i++) {
            $users[] = [
                'name' => "K6 User {$i}",
                'email' => "user{$i}@example.com",
                'email_verified_at' => $now,
                'password' => $password,
                'role' => 'customer',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Chunk insert for better performance
        foreach (array_chunk($users, 100) as $chunk) {
            User::insertOrIgnore($chunk);
        }
    }
}
