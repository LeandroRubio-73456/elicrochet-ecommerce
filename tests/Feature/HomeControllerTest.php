<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function home_page_loads_with_featured_products()
    {
        $featured = Product::factory()->create(['is_featured' => true, 'status' => 'active']);
        $regular = Product::factory()->create(['is_featured' => false, 'status' => 'active']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('featuredProducts', function ($products) use ($featured, $regular) {
            return $products->contains($featured) && ! $products->contains($regular);
        });
    }

    /** @test */
    public function shop_page_loads_and_filters_products()
    {
        $cheap = Product::factory()->create(['price' => 10, 'name' => 'Cheap Yarn', 'status' => 'active']);
        $expensive = Product::factory()->create(['price' => 100, 'name' => 'Expensive Yarn', 'status' => 'active']);
        $archived = Product::factory()->create(['status' => 'archived']);

        // Test Basic Load
        $response = $this->get(route('shop'));
        $response->assertOk();
        $response->assertSee('Cheap Yarn');
        $response->assertDontSee($archived->name); // Should only see active

        // Test Search
        $response = $this->get(route('shop', ['search' => 'Cheap']));
        $response->assertSee('Cheap Yarn');
        $response->assertDontSee('Expensive Yarn');

        // Test Price Filter
        $response = $this->get(route('shop', ['max_price' => 50]));
        $response->assertSee('Cheap Yarn');
        $response->assertDontSee('Expensive Yarn');
    }

    /** @test */
    public function product_detail_page_loads()
    {
        $product = Product::factory()->create(['status' => 'active']);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    /** @test */
    public function category_page_loads_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'status' => 'active']);
        $otherProduct = Product::factory()->create(['status' => 'active']); // Different category

        $response = $this->get(route('category.show', $category->slug));

        $response->assertOk();
        $response->assertSee($product->name);
        $response->assertDontSee($otherProduct->name);
    }

    /** @test */
    public function contact_form_submits_successfully()
    {
        $data = [
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'subject' => 'general',
            'message' => 'Hello there',
            'policy_check' => '1',
        ];

        $response = $this->post(route('contact.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function contact_form_validates_input()
    {
        $response = $this->post(route('contact.store'), []);
        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function static_pages_load()
    {
        $this->get(route('contact'))->assertOk();
        $this->get(route('bestseller'))->assertOk();
        $this->get(route('404'))->assertOk();
    }
}
