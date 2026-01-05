<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_products_relationship()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($category->products->contains($product));
    }

    /** @test */
    public function it_generates_correct_status_badge()
    {
        $active = Category::factory()->create(['status' => 'active']);
        $inactive = Category::factory()->create(['status' => 'inactive']);
        $archived = Category::factory()->create(['status' => 'archived']);

        $this->assertStringContainsString('Activo', $active->status_badge);
        $this->assertStringContainsString('bg-light-success', $active->status_badge);

        $this->assertStringContainsString('Inactivo', $inactive->status_badge);
        $this->assertStringContainsString('bg-light-warning', $inactive->status_badge);
    }

    /** @test */
    public function it_validates_required_specs_correctly()
    {
        $category = Category::factory()->create([
            'required_specs' => [
                ['name' => 'Color', 'type' => 'text', 'required' => true],
                ['name' => 'Size', 'type' => 'number', 'required' => false],
            ],
        ]);

        // Case 1: Missing required field
        $errors = $category->validateSpecs(['Size' => 10]);
        $this->assertContains("El campo 'Color' es obligatorio.", $errors);

        // Case 2: Invalid type
        $errors = $category->validateSpecs(['Color' => 'Red', 'Size' => 'NotNumber']);
        $this->assertContains("El campo 'Size' debe ser numérico.", $errors);

        // Case 3: Valid input
        $errors = $category->validateSpecs(['Color' => 'Red', 'Size' => 10]);
        $this->assertEmpty($errors);
    }
}
