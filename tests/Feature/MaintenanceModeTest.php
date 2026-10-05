<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    public function test_site_works_normally_and_maintenance_page_redirects_home_when_off(): void
    {
        $this->get('/')->assertOk();
        $this->get('/maintenance')->assertRedirect(route('home'));
    }

    public function test_visitors_are_sent_to_the_maintenance_page_when_on(): void
    {
        Setting::putMany(['maintenance_enabled' => '1', 'maintenance_back_at' => 'Monday, 10 am', 'whatsapp_number' => '919116684918']);

        $this->get('/')->assertRedirect(route('maintenance'));
        $this->get('/contact')->assertRedirect(route('maintenance'));

        $this->get('/maintenance')
            ->assertStatus(503)
            ->assertHeader('Retry-After', '3600')
            ->assertSee(config('settings.defaults.maintenance_heading'))
            ->assertSee('Monday, 10 am')
            ->assertSee('Chat on WhatsApp');

        $this->get('/admin/login')->assertOk();
    }

    public function test_admins_can_still_browse_the_site_during_maintenance(): void
    {
        Setting::putMany(['maintenance_enabled' => '1']);

        $this->actingAs($this->admin())->get('/')->assertOk();
        $this->get('/admin')->assertOk()->assertSee('The website is in maintenance mode');
    }

    public function test_admin_can_switch_maintenance_mode_on_from_settings(): void
    {
        $this->actingAs($this->admin())
            ->put('/admin/settings/maintenance', ['maintenance_enabled' => '1', 'maintenance_heading' => 'Back in a bit'])
            ->assertRedirect(route('admin.settings.edit', 'maintenance'));

        auth()->logout();

        $this->get('/shop')->assertRedirect(route('maintenance'));
        $this->get('/maintenance')->assertSee('Back in a bit');
    }
}
