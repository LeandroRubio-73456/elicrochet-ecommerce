<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            'pending_payment',
            'paid',
            'working',
            'shipped',
            'completed',
            'cancelled',
        ];

        // Ensure we have products and users
        if (Product::count() == 0) {
            $this->call(ProductsSeeder::class);
        }

        $users = User::where('role', 'customer')->get();
        if ($users->isEmpty()) {
            User::factory()->count(5)->create(['role' => 'customer']);
            $users = User::where('role', 'customer')->get();
        }

        foreach ($statuses as $status) {
            // Create 2 orders for each status
            Order::factory()
                ->count(2)
                ->create([
                    'status' => $status,
                    'user_id' => $users->random()->id,
                ])
                ->each(function ($order) {
                    // Create 1-3 items for each order
                    $products = Product::inRandomOrder()->take(rand(1, 3))->get();

                    foreach ($products as $product) {
                        OrderItem::factory()->create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'price' => $product->price,
                            'quantity' => rand(1, 2),
                        ]);
                    }

                    // Recalculate total
                    $order->recalculateTotal();
                    $order->save();
                });
        }

        $this->command->info('Created 12 dummy orders with various statuses.');
    }
}
