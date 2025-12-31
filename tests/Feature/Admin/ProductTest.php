<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user (assuming verification is required based on routes)
        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_products_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('back.products.index');
    }

    public function test_admin_can_view_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('back.products.create');
    }

    public function test_admin_can_create_product()
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $image = UploadedFile::fake()->image('product.jpg');

        $data = [
            'name' => 'New Product',
            'category_id' => $category->id,
            'description' => 'Product description',
            'price' => 100.50,
            'stock' => 10,
            'status' => 'active',
            'images' => [$image],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $data);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
        
        $product = Product::where('name', 'New Product')->first();
        $this->assertCount(1, $product->images);
    }

    public function test_admin_can_edit_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('back.products.edit');
        $response->assertViewHas('product', $product);
    }

    public function test_admin_can_update_product()
    {
        $product = Product::factory()->create();
        $newCategory = Category::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'category_id' => $newCategory->id,
            'description' => 'Updated description',
            'price' => 200,
            'stock' => 5,
            'status' => 'out_of_stock',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $data);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $response->assertStatus(200); // Controller returns JSON
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
