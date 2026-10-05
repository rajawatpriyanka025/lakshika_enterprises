<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\ProductSeo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSeoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->admin = User::factory()->create();
        $this->admin->forceFill(['is_admin' => true])->save();
    }

    public function test_new_products_always_get_stored_seo_fields(): void
    {
        $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Brass Table Lamp',
            'category_id' => Category::query()->create(['name' => 'Table Lamps', 'description' => 'Lamps for desks and bedsides.'])->id,
            'excerpt' => 'A warm brass lamp for bedside tables.',
            'description' => 'Details.',
            'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $product = Product::query()->where('slug', 'brass-table-lamp')->sole();

        $this->assertSame('Brass Table Lamp', $product->meta_title);
        $this->assertStringStartsWith('A warm brass lamp for bedside tables.', $product->meta_description);
        $this->assertLessThanOrEqual(ProductSeo::DESCRIPTION_MAX, mb_strlen($product->meta_description));
        $this->assertStringNotContainsStringIgnoringCase('clock', $product->meta_description);
    }

    public function test_bulk_editor_saves_every_product_and_regenerates_blanks(): void
    {
        $products = Product::query()->orderBy('id')->take(2)->get();

        $this->actingAs($this->admin)->get('/admin/product-seo')->assertOk()->assertSee($products[0]->name);

        $this->put('/admin/product-seo', ['products' => [
            $products[0]->id => ['focus_keyword' => 'round wall clock', 'meta_title' => 'Round Wall Clock, Minimal Design', 'meta_description' => 'Custom description.'],
            $products[1]->id => ['focus_keyword' => '', 'meta_title' => '', 'meta_description' => ''],
        ]])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertSame('round wall clock', $products[0]->fresh()->focus_keyword);
        $this->assertSame('Round Wall Clock, Minimal Design', $products[0]->fresh()->meta_title);
        $this->assertSame($products[1]->name, $products[1]->fresh()->meta_title);
        $this->assertNotEmpty($products[1]->fresh()->meta_description);
    }

    public function test_autofill_fills_only_missing_fields(): void
    {
        Product::query()->update(['meta_title' => null, 'meta_description' => null]);
        $kept = Product::query()->first();
        $kept->forceFill(['meta_title' => 'Keep This Title'])->saveQuietly();

        $this->actingAs($this->admin)->post('/admin/product-seo/autofill')->assertRedirect();

        $this->assertSame(0, Product::query()->whereNull('meta_title')->orWhereNull('meta_description')->count());
        $this->assertSame('Keep This Title', $kept->fresh()->meta_title);
    }

    public function test_seo_checklist_scores_keyword_usage(): void
    {
        $product = Product::query()->where('slug', 'easy-read-kitchen-wall-clock')->sole();
        $before = ProductSeo::score(ProductSeo::audit($product));

        $product->update([
            'focus_keyword' => 'kitchen wall clock',
            'meta_title' => 'Easy-Read Kitchen Wall Clock',
            'meta_description' => 'An easy-read kitchen wall clock with a clear dial you can check at a glance while cooking, placed safely away from heat and steam.',
            'material' => 'MDF', 'colour' => 'White', 'dimensions' => '30 cm',
        ]);

        $audit = collect(ProductSeo::audit($product->fresh()))->keyBy('label');
        $this->assertTrue($audit['Keyword in meta title']['pass']);
        $this->assertTrue($audit['Keyword in URL']['pass']);
        $this->assertTrue($audit['Specifications filled']['pass']);
        $this->assertGreaterThan($before, ProductSeo::score($audit->values()->all()));

        $this->actingAs($this->admin)->get("/admin/products/{$product->id}/edit")->assertOk()->assertSee('SEO checklist');
    }

    public function test_duplicate_meta_titles_are_flagged(): void
    {
        [$first, $second] = Product::query()->take(2)->get();
        $first->update(['meta_title' => 'Same Title For Both']);
        $second->update(['meta_title' => 'Same Title For Both']);

        $audit = collect(ProductSeo::audit($first->fresh()))->firstWhere('label', 'Unique meta title');
        $this->assertFalse($audit['pass']);
    }

    public function test_specifications_and_offer_schema_on_product_page(): void
    {
        $product = Product::query()->where('slug', 'easy-read-kitchen-wall-clock')->sole();

        $this->get('/products/easy-read-kitchen-wall-clock')->assertDontSee('"offers"', false);

        $product->update(['material' => 'Solid wood', 'colour' => 'Walnut', 'sku' => 'LE-001', 'price' => 1299, 'availability' => 'in_stock']);

        $this->get('/products/easy-read-kitchen-wall-clock')
            ->assertSee('Solid wood')
            ->assertSee('₹1,299')
            ->assertSee('"sku":"LE-001"', false)
            ->assertSee('"priceCurrency":"INR"', false)
            ->assertSee('"availability":"https://schema.org/InStock"', false);
    }
}
