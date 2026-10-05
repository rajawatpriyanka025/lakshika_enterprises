<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Guide;
use App\Models\Product;
use App\Models\Redirect;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->admin = User::factory()->create(['email' => 'admin@example.com']);
        $this->admin->forceFill(['is_admin' => true])->save();
    }

    public function test_guests_are_sent_to_login_and_non_admins_are_refused(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)->get('/admin')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_can_sign_in_and_out(): void
    {
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_non_admin_users_cannot_sign_in(): void
    {
        User::factory()->create(['email' => 'shopper@example.com']);

        $this->post('/admin/login', ['email' => 'shopper@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_every_admin_screen_renders(): void
    {
        $this->actingAs($this->admin);
        $product = Product::query()->first();

        foreach ([
            '/admin', '/admin/leads', '/admin/leads/create', '/admin/customers', '/admin/customers/create', '/admin/whatsapp-clicks',
            '/admin/products', '/admin/products/create', "/admin/products/{$product->id}/edit",
            '/admin/categories', '/admin/categories/create', '/admin/categories/'.$product->category_id.'/edit',
            '/admin/guides', '/admin/guides/create', '/admin/guides/'.Guide::query()->value('id').'/edit',
            '/admin/faqs', '/admin/faqs/create', '/admin/marketplaces', '/admin/marketplaces/create',
            '/admin/redirects', '/admin/redirects/create', '/admin/pages', '/admin/pages/home', '/admin/pages/contact',
            '/admin/settings/general', '/admin/settings/crm', '/admin/settings/social', '/admin/settings/seo', '/admin/account',
        ] as $url) {
            $this->get($url)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
    }

    public function test_product_can_be_created_with_image_and_seo_fields(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $this->post('/admin/products', [
            'name' => 'Walnut Pendulum Clock',
            'category_id' => Category::query()->value('id'),
            'excerpt' => 'A warm walnut clock with a gentle pendulum.',
            'description' => 'Long description.',
            'image' => UploadedFile::fake()->image('clock.jpg', 800, 800),
            'image_alt' => 'Walnut pendulum wall clock',
            'meta_title' => 'Walnut Pendulum Wall Clock',
            'meta_description' => 'A walnut pendulum wall clock for living rooms.',
            'is_active' => '1',
        ])->assertRedirect();

        $product = Product::query()->where('slug', 'walnut-pendulum-clock')->firstOrFail();
        Storage::disk('public')->assertExists($product->image_path);

        $this->get('/products/walnut-pendulum-clock')
            ->assertOk()
            ->assertSee('<title>Walnut Pendulum Wall Clock | Lakshika Enterprises</title>', false)
            ->assertSee('A walnut pendulum wall clock for living rooms.')
            ->assertSee('alt="Walnut pendulum wall clock"', false);
    }

    public function test_changing_a_slug_creates_a_redirect_from_the_old_url(): void
    {
        $this->actingAs($this->admin);
        $product = Product::query()->where('slug', 'minimal-round-wall-clock')->firstOrFail();

        $this->put("/admin/products/{$product->id}", [
            'name' => $product->name,
            'slug' => 'minimal-round-clock',
            'category_id' => $product->category_id,
            'excerpt' => $product->excerpt,
            'description' => $product->description,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('redirects', ['from_path' => '/products/minimal-round-wall-clock', 'to_url' => '/products/minimal-round-clock']);
        $this->get('/products/minimal-round-wall-clock')->assertRedirect(url('/products/minimal-round-clock'))->assertStatus(301);
        $this->assertSame(1, Redirect::query()->value('hits'));
    }

    public function test_hidden_products_and_draft_guides_are_not_public(): void
    {
        Product::query()->where('slug', 'minimal-round-wall-clock')->update(['is_active' => false]);
        Guide::query()->where('slug', 'how-to-choose-a-wall-clock')->update(['is_published' => false]);

        $this->get('/products/minimal-round-wall-clock')->assertNotFound();
        $this->get('/guides/how-to-choose-a-wall-clock')->assertNotFound();
        $this->get('/sitemap.xml')
            ->assertDontSee('/products/minimal-round-wall-clock')
            ->assertDontSee('/guides/how-to-choose-a-wall-clock');
    }

    public function test_page_copy_and_settings_are_editable(): void
    {
        $this->actingAs($this->admin);

        $this->put('/admin/pages/home', [
            'content' => ['hero_heading' => "Clocks with *character*"],
            'meta_title' => 'Designer Wall Clocks Online',
        ])->assertRedirect();

        $this->put('/admin/settings/general', ['site_name' => 'Lakshika Enterprises', 'announcement' => 'Free delivery this week'])->assertRedirect();

        $this->get('/')
            ->assertSee('Clocks with <em>character</em>', false)
            ->assertSee('<title>Designer Wall Clocks Online | Lakshika Enterprises</title>', false)
            ->assertSee('Free delivery this week');
    }

    public function test_tracking_ids_are_validated_before_output(): void
    {
        $this->actingAs($this->admin);

        $this->put('/admin/settings/seo', ['ga4_measurement_id' => '"><script>alert(1)</script>'])
            ->assertSessionHasErrors('ga4_measurement_id');

        $this->put('/admin/settings/seo', ['ga4_measurement_id' => 'G-ABC123XYZ', 'google_site_verification' => 'abcDEF1234567890'])->assertSessionHasNoErrors();

        $this->get('/')
            ->assertSee('googletagmanager.com/gtag/js?id=G-ABC123XYZ', false)
            ->assertSee('<meta name="google-site-verification" content="abcDEF1234567890">', false);
    }

    public function test_faqs_are_managed_in_admin(): void
    {
        $this->actingAs($this->admin);

        $this->post('/admin/faqs', ['question' => 'Do you ship across India?', 'answer' => 'Yes, through our marketplace partners.', 'is_active' => '1'])->assertRedirect();
        $this->get('/faqs')->assertSee('Do you ship across India?');

        $this->delete('/admin/faqs/'.Faq::query()->where('question', 'Do you ship across India?')->value('id'))->assertRedirect();
        $this->get('/faqs')->assertDontSee('Do you ship across India?');
    }

    public function test_discourage_indexing_hides_the_whole_site(): void
    {
        Setting::putMany(['discourage_indexing' => '1']);

        $this->get('/')->assertSee('<meta name="robots" content="noindex, follow">', false);
        $this->get('/robots.txt')->assertSee("Disallow: /\n", false);
    }
}
