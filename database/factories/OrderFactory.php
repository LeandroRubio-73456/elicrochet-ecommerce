<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(), // Assuming Address factory exists, otherwise might need failing or create logic
            'total_amount' => $this->faker->randomFloat(2, 20, 500),
            'status' => 'pending_payment',
            'payphone_transaction_id' => $this->faker->uuid,
            'payphone_status' => 'Pending',
            'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->safeEmail,
            'customer_phone' => $this->faker->phoneNumber,
            'type' => 'standard',
            'shipping_address' => $this->faker->address,
            'shipping_city' => $this->faker->city,
            'shipping_zip' => $this->faker->postcode,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
