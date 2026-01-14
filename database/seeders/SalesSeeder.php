<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('es_ES');

        // Ensure we have products
        if (Product::count() < 5) {
            $this->command->info('Creating dummy products first...');
            Product::factory()->count(10)->create();
        }

        $products = Product::all();
        $statuses = ['paid', 'completed', 'shipped'];

        // Create 200 orders distributed over the last 6 months
        $this->command->info('Generando 200 ventas históricas...');

        for ($i = 0; $i < 200; $i++) {

            // Random date in last 6 months
            $date = Carbon::now()->subDays(rand(0, 180));

            // Random status (mostly specific completed/paid statuses for charts)
            $status = $faker->randomElement($statuses);

            // Create Order
            $order = Order::create([
                'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
                'total_amount' => 0, // Will recalculate
                'status' => $status,
                'payphone_transaction_id' => $faker->uuid,
                'payphone_status' => 'Approved',
                'customer_name' => $faker->name,
                'customer_email' => $faker->email,
                'customer_phone' => $faker->phoneNumber,
                'type' => 'standard',
                'shipping_address' => $faker->address,
                'shipping_city' => $faker->city,
                'shipping_zip' => $faker->postcode,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Add Order Items (1-5 items per order)
            $itemCount = rand(1, 5);
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $qty = rand(1, 3);
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ]);

                $total += $qty * $price;
            }

            // Update Order Total
            $order->total_amount = $total;
            $order->save();
        }

        $this->command->info('Seeding de Ventas y Reportes completado con éxito!');
    }
}
