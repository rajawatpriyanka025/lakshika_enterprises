<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Guide;
use App\Models\Lead;
use App\Models\Product;
use App\Models\WhatsappClick;
use Illuminate\Contracts\View\View;

class DashboardController extends AdminController
{
    public function __invoke(): View
    {
        $since = now()->subDays(30);

        return view('admin.dashboard', [
            'stats' => [
                'new_leads' => Lead::query()->where('status', 'new')->count(),
                'leads_30' => Lead::query()->where('created_at', '>=', $since)->count(),
                'won_30' => Lead::query()->where('status', 'won')->where('updated_at', '>=', $since)->count(),
                'whatsapp_30' => WhatsappClick::query()->where('created_at', '>=', $since)->count(),
                'customers' => Customer::query()->count(),
                'products' => Product::query()->active()->count(),
            ],
            'pipeline' => Lead::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'followUps' => Lead::query()->followUpDue()->with('product')->orderBy('follow_up_at')->limit(8)->get(),
            'recentLeads' => Lead::query()->with('product')->latest()->limit(8)->get(),
            'topProducts' => Product::query()
                ->withCount(['leads' => fn ($query) => $query->where('created_at', '>=', $since)])
                ->orderByDesc('leads_count')
                ->limit(5)
                ->get()
                ->where('leads_count', '>', 0),
            'seoIssues' => $this->seoIssues(),
        ]);
    }

    /** Quick SEO health checks so gaps are visible without an external tool. */
    private function seoIssues(): array
    {
        $issues = [];

        $add = function (string $label, $items, callable $link) use (&$issues) {
            if ($items->isNotEmpty()) {
                $issues[] = ['label' => $label, 'items' => $items->map(fn ($item) => ['name' => $item->name ?? $item->title, 'url' => $link($item)])];
            }
        };

        $products = Product::query()->active()->get();
        $categories = Category::query()->active()->get();
        $guides = Guide::query()->published()->get();

        $add('Products without a photo', $products->whereNull('image_path'), fn ($item) => route('admin.products.edit', $item));
        $add('Products without a meta description', $products->filter(fn ($item) => blank($item->meta_description)), fn ($item) => route('admin.products.edit', $item));
        $add('Products with a thin description (under 300 characters)', $products->filter(fn ($item) => mb_strlen(strip_tags($item->description)) < 300), fn ($item) => route('admin.products.edit', $item));
        $add('Meta titles over 60 characters', $products->concat($categories)->concat($guides)->filter(fn ($item) => mb_strlen((string) $item->meta_title) > 60), fn ($item) => match (true) {
            $item instanceof Product => route('admin.products.edit', $item),
            $item instanceof Category => route('admin.categories.edit', $item),
            default => route('admin.guides.edit', $item),
        });
        $add('Collections without a meta description', $categories->filter(fn ($item) => blank($item->meta_description)), fn ($item) => route('admin.categories.edit', $item));
        $add('Guides without a meta description', $guides->filter(fn ($item) => blank($item->meta_description)), fn ($item) => route('admin.guides.edit', $item));

        foreach (['ga4_measurement_id' => 'Google Analytics', 'google_site_verification' => 'Google Search Console verification'] as $key => $label) {
            if (! setting($key) && ! ($key === 'ga4_measurement_id' && setting('gtm_container_id'))) {
                $issues[] = ['label' => $label.' is not set up', 'items' => collect([['name' => 'Open SEO settings', 'url' => route('admin.settings.edit', 'seo')]])];
            }
        }

        if (setting('discourage_indexing')) {
            array_unshift($issues, ['label' => 'The whole site is hidden from search engines', 'items' => collect([['name' => 'Turn off in SEO settings', 'url' => route('admin.settings.edit', 'seo')]]), 'critical' => true]);
        }

        return $issues;
    }
}
