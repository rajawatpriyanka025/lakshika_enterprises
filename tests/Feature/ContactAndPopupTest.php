<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAndPopupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_contact_page_shows_details_map_and_reviews(): void
    {
        Setting::putMany([
            'contact_phone' => '+91 91166 84918',
            'contact_email' => 'hello@example.com',
            'business_address' => 'Malviya Nagar, Jaipur, Rajasthan',
            'business_hours' => 'Mon – Sat: 10 am – 7 pm',
            'whatsapp_number' => '919116684918',
            'google_rating' => '4.8',
            'google_review_count' => '57',
            'google_reviews_url' => 'https://g.page/r/example',
            'google_review_write_url' => 'https://g.page/r/example/review',
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('href="tel:+919116684918"', false)
            ->assertSee('hello@example.com')
            ->assertSee('+91 91166 84918')
            ->assertSee('Mon – Sat: 10 am – 7 pm')
            ->assertSee('maps.google.com/maps?q=', false)
            ->assertSee('Rated 4.8 out of 5 on Google')
            ->assertSee('57 reviews')
            ->assertSee('Write a review')
            ->assertSee('"@type":"LocalBusiness"', false);
    }

    public function test_pasted_map_iframe_is_reduced_to_a_safe_google_embed(): void
    {
        Setting::putMany(['map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!abc" width="600"></iframe>']);
        $this->get('/contact')->assertSee('src="https://www.google.com/maps/embed?pb=!1m18!abc"', false);

        Setting::putMany(['map_embed' => '<iframe src="https://evil.example/phish"></iframe>', 'business_address' => '']);
        $this->get('/contact')->assertDontSee('evil.example')->assertDontSee('<iframe', false);
    }

    public function test_welcome_popup_is_on_by_default_and_can_be_switched_off(): void
    {
        $this->get('/')
            ->assertSee('id="welcome-popup"', false)
            ->assertSee('Welcome to Lakshika Enterprises');

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->put('/admin/settings/popup', [
            'popup_heading' => 'Festive offers are live',
            'popup_message' => 'Ask us about Diwali gifting.',
            'popup_delay' => 2,
            'popup_frequency_days' => 3,
        ])->assertSessionHasNoErrors();

        // popup_enabled was left unticked, so it must stay off rather than fall back to the default.
        $this->get('/')->assertDontSee('id="welcome-popup"', false);

        $this->put('/admin/settings/popup', ['popup_enabled' => '1', 'popup_heading' => 'Festive offers are live', 'popup_delay' => 2, 'popup_frequency_days' => 3]);
        $this->get('/')
            ->assertSee('Festive offers are live')
            ->assertSee('data-delay="2"', false)
            ->assertSee('data-days="3"', false);
    }

    public function test_popup_whatsapp_button_needs_a_number(): void
    {
        $this->get('/')->assertDontSee('Chat on WhatsApp');

        Setting::putMany(['whatsapp_number' => '919116684918']);

        $this->get('/')->assertSee('Chat on WhatsApp');
    }
}
