<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the maximum user_id from the users table
        $maxUserId = User::max('id');

        if ($maxUserId === null) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Create hundreds of orders with random user_id
        $orders = [];
        for ($i = 1; $i <= 100; $i++) {
            $orders[] = [
                'user_id' => rand(1, $maxUserId),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Order::insert($orders);

        $this->command->info("Created 100 orders with user IDs between 1 and {$maxUserId}.");
    }
}