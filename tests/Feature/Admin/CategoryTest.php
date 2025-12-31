<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_view_categories_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('back.categories.index');
    }

    /** @test */
    public function index_returns_json_for_ajax()
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index', ['ajax' => true]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
    }

    /** @test */
    public function admin_can_create_category()
    {
        $data = [
            'name' => 'New Category',
            'description' => 'Description test',
            'status' => 'active',
            'icon' => 'ti-box',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'New Category', 'slug' => 'new-category']);
    }

    /** @test */
    public function admin_can_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Updated Name',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Name']);
    }

    /** @test */
    public function admin_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function creates_unique_slug_automatically()
    {
        Category::factory()->create(['slug' => 'test-category']);

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Test Category', // This generates 'test-category' again
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['slug' => 'test-category-1']);
    }

    /** @test */
    public function processes_required_specs_json()
    {
        $specs = [['name' => 'Color', 'type' => 'text']];
        
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'New Cat',
            'status' => 'active',
            'required_specs' => json_encode($specs)
        ]);

        $category = Category::where('name', 'New Cat')->first();
        $this->assertEquals($specs, $category->required_specs);
    }

    /** @test */
    public function datatables_can_filter_and_sort()
    {
        Category::factory()->create(['name' => 'Active Cat', 'status' => 'active']);
        Category::factory()->create(['name' => 'Old Cat', 'status' => 'archived']);

        // Status filter (Column 5)
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index', [
            'ajax' => 1,
            'columns' => [
                ['data' => 'id'],['data' => 'icon'],['data' => 'name'],['data' => 'slug'],['data' => 'products_count'],
                ['data' => 'status', 'search' => ['value' => 'active']]
            ],
            'order' => [['column' => 2, 'dir' => 'desc']] // Name
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertJsonCount(1, 'data');
    }
}
