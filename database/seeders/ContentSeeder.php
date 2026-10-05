<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Guide;
use App\Models\Marketplace;
use Illuminate\Database\Seeder;

/**
 * Seeds guides, FAQs and marketplaces only when each table is empty,
 * so re-running it never overwrites edits made in the admin.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $content = require database_path('seeders/data/content.php');

        if (Marketplace::query()->doesntExist()) {
            foreach ([
                ['name' => 'Amazon', 'search_url' => 'https://www.amazon.in/s', 'search_parameter' => 'k'],
                ['name' => 'Flipkart', 'search_url' => 'https://www.flipkart.com/search', 'search_parameter' => 'q'],
                ['name' => 'Meesho', 'search_url' => 'https://www.meesho.com/search', 'search_parameter' => 'q'],
            ] as $index => $marketplace) {
                Marketplace::query()->create($marketplace + ['sort_order' => $index, 'is_active' => true]);
            }
        }

        if (Guide::query()->doesntExist()) {
            foreach ($content['guides'] as $index => $guide) {
                Guide::query()->create([
                    'title' => $guide['title'],
                    'slug' => $guide['slug'],
                    'intro' => $guide['intro'],
                    'sections' => $guide['sections'],
                    'meta_description' => $guide['meta_description'],
                    'is_published' => true,
                    'published_at' => now(),
                    'sort_order' => $index,
                ]);
            }
        }

        if (Faq::query()->doesntExist()) {
            foreach ($content['faqs'] as $index => $faq) {
                Faq::query()->create($faq + ['sort_order' => $index, 'is_active' => true]);
            }
        }
    }
}
