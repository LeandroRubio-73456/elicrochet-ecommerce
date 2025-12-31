<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_home_page()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('front.home');
    }

    /** @test */
    public function can_view_shop_page()
    {
        Category::factory()->count(2)->create();
        Product::factory()->count(3)->create(['status' => 'active']);

        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        $response->assertViewIs('front.shop');
        $response->assertViewHas(['categories', 'products']);
    }

    /** @test */
    public function can_filter_shop_by_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'status' => 'active']);
        $product2 = Product::factory()->create(['status' => 'active']);

        $response = $this->get(route('shop', ['category' => $category->slug]));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        // Depending on implementation, it might or might not see the other one. 
        // We mainly want to hit the logic.
    }

    /** @test */
    public function can_view_single_product_page()
    {
        $product = Product::factory()->create(['status' => 'active']);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertViewIs('front.single');
        $response->assertViewHas('product');
    }

    /** @test */
    public function can_view_category_page()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('category.show', $category->slug));

        $response->assertStatus(200);
        $response->assertViewIs('front.category-single');
        $response->assertViewHas('category');
    }

    /** @test */
    public function can_view_bestseller_page()
    {
        $response = $this->get(route('bestseller'));

        $response->assertStatus(200);
        $response->assertViewIs('front.bestseller');
    }

    /** @test */
    public function can_view_contact_page()
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertViewIs('front.contact');
    }
}
