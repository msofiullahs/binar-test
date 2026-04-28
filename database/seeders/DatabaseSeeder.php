<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users first (1 administrator, 5 managers, dozens of users)
        $this->call([
            UserSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
