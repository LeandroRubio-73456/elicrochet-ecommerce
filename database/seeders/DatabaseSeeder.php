<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        $this->call([
            CategoriesSeeder::class,
            ProductsSeeder::class,
        ]);
        
        Schema::enableForeignKeyConstraints();

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@elicrochet.com',
            'password' => bcrypt('password'), // password
            'role' => 'admin',
        ]);
    }
}
