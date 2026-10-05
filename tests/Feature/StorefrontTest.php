<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_homepage_renders_brand_and_seo_metadata(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Lakshika Enterprises')
            ->assertSee('name="description"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee(route('collections.show', 'modern-wall-clocks'), false);
    }

    public function test_catalog_can_be_searched_and_products_have_detail_pages(): void
    {
        $this->get('/shop?q=kitchen')
            ->assertOk()
            ->assertSee('Easy-Read Kitchen Wall Clock')
            ->assertDontSee('Minimal Round Wall Clock');

        $this->get('/products/easy-read-kitchen-wall-clock')
            ->assertOk()
            ->assertSee('Search on Amazon')
            ->assertSee('https://schema.org', false)
            ->assertSee('Marketplace pages open in a new tab.')
            ->assertSee('Marketplace pages open in a new tab.');
    }

    public function test_collection_and_buying_guide_pages_render(): void
    {
        $this->get('/collections/living-room-wall-clocks')
            ->assertOk()
            ->assertSee('Living Room Wall Clocks');

        $this->get('/guides/how-to-choose-a-wall-clock')
            ->assertOk()
            ->assertSee('Choose a size that fits the wall');

        $this->get('/faqs')
            ->assertOk()
            ->assertSee('Are prices and availability shown on this website?');
    }

    public function test_sitemap_and_robots_include_public_routes(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('products.show', 'minimal-round-wall-clock'), false)
            ->assertSee(route('guides.show', 'how-to-choose-a-wall-clock'), false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_unknown_guide_returns_not_found(): void
    {
        $this->get('/guides/not-a-real-guide')->assertNotFound();
    }
}
