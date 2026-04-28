<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 administrator
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'administrator',
            'active' => true,
            'remember_token' => Str::random(10),
        ]);

        // Create 5 managers
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Manager {$i}",
                'email' => "manager{$i}@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'manager',
                'active' => true,
                'remember_token' => Str::random(10),
            ]);
        }

        // Create dozens (24) regular users, some with active = false
        for ($i = 1; $i <= 24; $i++) {
            User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'active' => $i % 3 !== 0, // Every 3rd user will be inactive
                'remember_token' => Str::random(10),
            ]);
        }
    }
}