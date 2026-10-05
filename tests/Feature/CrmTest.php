<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsappClick;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
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

    public function test_there_is_no_enquiry_form(): void
    {
        $this->post('/enquiry', ['name' => 'Asha'])->assertNotFound();
        $this->get('/contact')->assertDontSee('name="phone"', false)->assertDontSee('method="post"', false);
        $this->get('/products/easy-read-kitchen-wall-clock')->assertDontSee('name="phone"', false);
    }

    public function test_whatsapp_buttons_appear_only_when_a_number_is_set(): void
    {
        $this->get('/whatsapp')->assertNotFound();
        $this->get('/products/easy-read-kitchen-wall-clock')->assertDontSee('on WhatsApp');

        Setting::putMany(['whatsapp_number' => '919876543210']);

        $this->get('/products/easy-read-kitchen-wall-clock')
            ->assertSee('Enquire about this product on WhatsApp')
            ->assertSee(e(route('whatsapp', ['product' => 'easy-read-kitchen-wall-clock', 'type' => 'bulk'])), false);
        $this->get('/contact')->assertSee('Bulk / wholesale order');
    }

    public function test_product_enquiry_opens_whatsapp_with_product_and_topic(): void
    {
        Setting::putMany(['whatsapp_number' => '91 98765 43210']);

        $response = $this->get('/whatsapp?product=easy-read-kitchen-wall-clock&type=bulk');

        $location = urldecode($response->headers->get('Location'));
        $this->assertStringStartsWith('https://wa.me/919876543210?text=', $location);
        $this->assertStringContainsString('Easy-Read Kitchen Wall Clock', $location);
        $this->assertStringContainsString('Enquiry: Bulk / wholesale order', $location);
        $this->assertStringContainsString('Quantity:', $location);

        $click = WhatsappClick::query()->sole();
        $this->assertSame('bulk', $click->topic);
        $this->assertSame('easy-read-kitchen-wall-clock', $click->product->slug);
    }

    public function test_product_page_button_defaults_to_a_product_question(): void
    {
        Setting::putMany(['whatsapp_number' => '919876543210']);

        $this->get('/whatsapp?product=easy-read-kitchen-wall-clock');

        $this->assertSame('product', WhatsappClick::query()->sole()->topic);
    }

    public function test_bots_and_bad_topics_are_handled(): void
    {
        Setting::putMany(['whatsapp_number' => '919876543210']);

        $this->withHeader('User-Agent', 'Googlebot/2.1')->get('/whatsapp')->assertRedirect();
        $this->assertSame(0, WhatsappClick::query()->count());

        $this->withHeader('User-Agent', 'Mozilla/5.0')->get('/whatsapp?type=<script>');
        $this->assertNull(WhatsappClick::query()->sole()->topic);
    }

    public function test_whatsapp_click_can_be_logged_as_a_lead(): void
    {
        Setting::putMany(['whatsapp_number' => '919876543210']);
        $product = Product::query()->where('slug', 'easy-read-kitchen-wall-clock')->sole();
        $this->get('/whatsapp?product=easy-read-kitchen-wall-clock&type=dealer');

        $this->actingAs($this->admin)->get('/admin/whatsapp-clicks')
            ->assertOk()
            ->assertSee('Become a dealer')
            ->assertSee(e(route('admin.leads.create', ['source' => 'whatsapp', 'type' => 'dealer', 'product' => $product->id])), false);

        $this->get(route('admin.leads.create', ['source' => 'whatsapp', 'type' => 'dealer', 'product' => $product->id]))
            ->assertOk()
            ->assertSee('<option value="'.$product->id.'" selected', false);

        $this->post('/admin/leads', [
            'name' => 'Asha Verma', 'phone' => '+91 98765 43210', 'city' => 'Jaipur',
            'enquiry_type' => 'dealer', 'product_id' => $product->id, 'source' => 'whatsapp', 'status' => 'new',
        ])->assertRedirect();

        $lead = Lead::query()->sole();
        $this->assertSame('whatsapp', $lead->source);
        $this->assertSame('9876543210', $lead->customer->phone);
    }

    public function test_repeat_leads_attach_to_the_same_customer(): void
    {
        $this->actingAs($this->admin);
        $payload = ['name' => 'Ravi', 'enquiry_type' => 'general', 'source' => 'whatsapp', 'status' => 'new'];

        $this->post('/admin/leads', ['phone' => '9876543210'] + $payload);
        $this->post('/admin/leads', ['phone' => '+91-98765-43210'] + $payload);

        $this->assertSame(1, Customer::query()->count());
        $this->assertSame(2, Customer::query()->first()->leads()->count());
    }

    public function test_utm_campaign_is_recorded_on_whatsapp_clicks(): void
    {
        Setting::putMany(['whatsapp_number' => '919876543210']);

        $this->get('/?utm_source=instagram&utm_medium=social&utm_campaign=diwali');
        $this->get('/whatsapp');

        $this->assertSame('instagram', WhatsappClick::query()->sole()->utm_source);
    }

    public function test_admin_can_work_a_lead(): void
    {
        $this->actingAs($this->admin)->post('/admin/leads', ['name' => 'Neha', 'phone' => '9000000002', 'enquiry_type' => 'dealer', 'source' => 'whatsapp', 'status' => 'new']);
        $lead = Lead::query()->sole();

        $this->get("/admin/leads/{$lead->id}")->assertOk()->assertSee('Neha');

        $this->post("/admin/leads/{$lead->id}/notes", ['body' => 'Called, sending catalogue.'])->assertRedirect();
        $this->assertSame('contacted', $lead->fresh()->status);

        $this->put("/admin/leads/{$lead->id}", [
            'status' => 'won', 'source' => 'whatsapp', 'enquiry_type' => 'dealer', 'follow_up_at' => now()->addDay()->toDateString(),
        ])->assertRedirect();
        $this->assertSame('won', $lead->fresh()->status);
        $this->assertSame(2, $lead->notes()->count()); // the note plus the automatic "status changed" note

        $this->assertStringContainsString('Neha', $this->get('/admin/leads/export')->assertOk()->streamedContent());
    }

    public function test_csv_export_neutralises_spreadsheet_formulas(): void
    {
        $this->actingAs($this->admin)->post('/admin/leads', ['name' => '=HYPERLINK("http://evil")', 'phone' => '9000000003', 'enquiry_type' => 'general', 'source' => 'phone', 'status' => 'new']);

        $this->assertStringContainsString("'=HYPERLINK", $this->get('/admin/leads/export')->streamedContent());
    }
}
