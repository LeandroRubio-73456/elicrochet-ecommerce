<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function admin_can_delete_product_image()
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $image = ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/test.jpg',
        ]);

        Storage::disk('public')->put('products/test.jpg', 'fake content');

        $response = $this->actingAs($this->admin)->delete(route('admin.products.images.destroy', $image));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('products/test.jpg');
    }
}
