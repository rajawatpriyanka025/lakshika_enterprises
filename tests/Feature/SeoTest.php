<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_pages_have_complete_social_and_search_metadata(): void
    {
        $this->get('/products/easy-read-kitchen-wall-clock')
            ->assertSee('<link rel="canonical" href="'.url('/products/easy-read-kitchen-wall-clock').'">', false)
            ->assertSee('<meta property="og:type" content="product">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('"@type":"Product"', false);
    }

    public function test_home_page_has_organization_and_website_schema(): void
    {
        $this->get('/')
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"@type":"SearchAction"', false)
            ->assertSee('images/brand/logo-square.png', false);
    }

    public function test_search_results_are_noindexed_and_canonicalised(): void
    {
        $this->get('/shop?q=kitchen')
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="'.route('shop').'">', false);
    }

    public function test_noindex_products_are_left_out_of_the_sitemap(): void
    {
        Product::query()->where('slug', 'easy-read-kitchen-wall-clock')->update(['noindex' => true]);

        $this->get('/products/easy-read-kitchen-wall-clock')->assertSee('noindex, follow');
        $this->get('/sitemap.xml')->assertDontSee('easy-read-kitchen-wall-clock');
    }

    public function test_robots_txt_blocks_private_areas(): void
    {
        $this->get('/robots.txt')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /whatsapp')
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_guides_have_article_schema_with_dates(): void
    {
        $this->get('/guides/how-to-choose-a-wall-clock')
            ->assertOk()
            ->assertSee('"@type":"Article"', false)
            ->assertSee('"datePublished"', false);
    }
}
