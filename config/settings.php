<?php

/*
| Site-wide settings editable in Admin → Settings. Values are stored in the
| `settings` table; the defaults below apply until a value is saved.
*/

return [
    'defaults' => [
        'site_name' => env('STOREFRONT_NAME', 'Lakshika Enterprises'),
        'tagline' => 'Thoughtfully chosen pieces for the home.',
        'announcement' => 'A little more character for the walls you call home',
        'footer_text' => 'Thoughtful products for everyday spaces.',
        'whatsapp_message' => 'Hi Lakshika Enterprises, I would like to know more about your products.',
        'popup_enabled' => '1',
        'popup_heading' => 'Welcome to Lakshika Enterprises',
        'popup_message' => "Thoughtfully chosen pieces for your home. Have a question, or need a bulk or dealer order? We're happy to help.",
        'popup_button_label' => 'Explore the collection',
        'popup_button_url' => '/shop',
        'popup_show_whatsapp' => '1',
        'popup_delay' => '4',
        'popup_frequency_days' => '7',
        'default_meta_description' => 'Shop home products from Lakshika Enterprises, available on Amazon, Flipkart and Meesho. Send an enquiry for bulk and dealer orders.',
    ],

    'groups' => [
        'general' => [
            'label' => 'Business details',
            'fields' => [
                'site_name' => ['label' => 'Business name', 'rules' => 'required|string|max:80'],
                'tagline' => ['label' => 'Tagline', 'rules' => 'nullable|string|max:160'],
                'announcement' => ['label' => 'Announcement bar text', 'rules' => 'nullable|string|max:140', 'help' => 'Leave empty to hide the bar.'],
                'footer_text' => ['label' => 'Footer text', 'rules' => 'nullable|string|max:240'],
                'contact_email' => ['label' => 'Contact email', 'type' => 'email', 'rules' => 'nullable|email|max:120'],
                'contact_phone' => ['label' => 'Contact phone', 'rules' => 'nullable|string|max:30', 'help' => 'Shown on the contact page and in Organization schema, e.g. +91 98765 43210.'],
                'business_address' => ['label' => 'Business address', 'type' => 'textarea', 'rules' => 'nullable|string|max:400'],
            ],
        ],
        'crm' => [
            'label' => 'Leads & WhatsApp',
            'fields' => [
                'whatsapp_number' => ['label' => 'WhatsApp number', 'rules' => ['nullable', 'regex:/^\+?[0-9 ]{10,16}$/'], 'help' => 'With country code, e.g. 919876543210. Leave empty to hide WhatsApp buttons.'],
                'whatsapp_message' => ['label' => 'WhatsApp opening message', 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'help' => 'Every enquiry button starts the chat with this. Product and bulk/dealer buttons add the product name and topic automatically.'],
            ],
        ],
        'contact' => [
            'label' => 'Contact page, map & reviews',
            'fields' => [
                'business_hours' => ['label' => 'Business hours', 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'help' => 'One line per day range, e.g. "Mon – Sat: 10:00 am – 7:00 pm".'],
                'map_embed' => ['label' => 'Google Maps embed', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000',
                    'help' => 'In Google Maps, open your business → Share → Embed a map → Copy HTML, and paste it here. Leave empty to show a map of the business address.'],
                'google_maps_url' => ['label' => 'Google Maps link (directions)', 'type' => 'url', 'rules' => 'nullable|url|max:500', 'help' => 'Share → Copy link from your Google Maps listing.'],
                'google_reviews_url' => ['label' => 'Google reviews page', 'type' => 'url', 'rules' => 'nullable|url|max:500', 'help' => 'The link to your Google Business Profile reviews.'],
                'google_review_write_url' => ['label' => '"Write a review" link', 'type' => 'url', 'rules' => 'nullable|url|max:500', 'help' => 'From Google Business Profile → Ask for reviews → copy the link.'],
                'google_rating' => ['label' => 'Google rating (out of 5)', 'type' => 'number', 'rules' => 'nullable|numeric|min:1|max:5', 'help' => 'Copy it from your Google profile, e.g. 4.8. Leave empty to hide the stars.'],
                'google_review_count' => ['label' => 'Number of Google reviews', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:1000000'],
            ],
        ],
        'popup' => [
            'label' => 'Welcome popup',
            'fields' => [
                'popup_enabled' => ['label' => 'Show the welcome popup', 'type' => 'checkbox', 'rules' => 'nullable|boolean', 'help' => 'Greets each visitor once, then stays hidden for the number of days below.'],
                'popup_heading' => ['label' => 'Heading', 'rules' => 'nullable|string|max:80'],
                'popup_message' => ['label' => 'Message', 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'help' => 'Keep it short: one or two sentences.'],
                'popup_image' => ['label' => 'Image (optional)', 'type' => 'image', 'rules' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', 'help' => 'Shown at the top of the popup. Landscape, about 800 × 400 px, under 2 MB.'],
                'popup_button_label' => ['label' => 'Button text', 'rules' => 'nullable|string|max:40', 'help' => 'e.g. "Explore the collection" or "Send an enquiry".'],
                'popup_button_url' => ['label' => 'Button link', 'rules' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|\/)/'], 'help' => 'A page on this site such as /shop or /contact#enquiry, or a full https:// address.'],
                'popup_show_whatsapp' => ['label' => 'Also show a "Chat on WhatsApp" button', 'type' => 'checkbox', 'rules' => 'nullable|boolean', 'help' => 'Only appears when a WhatsApp number is set.'],
                'popup_delay' => ['label' => 'Seconds before it appears', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:60', 'help' => 'A few seconds lets visitors see the page first.'],
                'popup_frequency_days' => ['label' => 'Show again after (days)', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:365', 'help' => '0 shows it on every visit (once per browser session). Changing the heading or message shows it again to everyone.'],
            ],
        ],
        'social' => [
            'label' => 'Social profiles',
            'fields' => [
                'instagram_url' => ['label' => 'Instagram', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
                'facebook_url' => ['label' => 'Facebook', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
                'youtube_url' => ['label' => 'YouTube', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
                'pinterest_url' => ['label' => 'Pinterest', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
                'linkedin_url' => ['label' => 'LinkedIn', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
                'x_url' => ['label' => 'X (Twitter)', 'type' => 'url', 'rules' => 'nullable|url|max:255'],
            ],
        ],
        'seo' => [
            'label' => 'SEO & tracking',
            'fields' => [
                'default_meta_description' => ['label' => 'Default meta description', 'type' => 'textarea', 'rules' => 'nullable|string|max:320', 'help' => 'Used when a page has no description of its own.'],
                'default_og_image' => ['label' => 'Default social share image', 'type' => 'image', 'rules' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', 'help' => '1200 × 630 px works best. Falls back to the square logo.'],
                'google_site_verification' => ['label' => 'Google Search Console verification code', 'rules' => ['nullable', 'regex:/^[A-Za-z0-9_\-]{10,100}$/'], 'help' => 'Only the content="…" value of the meta tag.'],
                'bing_site_verification' => ['label' => 'Bing Webmaster verification code', 'rules' => ['nullable', 'regex:/^[A-Za-z0-9_\-]{10,100}$/']],
                'ga4_measurement_id' => ['label' => 'Google Analytics 4 ID', 'rules' => ['nullable', 'regex:/^G-[A-Z0-9]{4,20}$/'], 'help' => 'e.g. G-XXXXXXXXXX'],
                'gtm_container_id' => ['label' => 'Google Tag Manager ID', 'rules' => ['nullable', 'regex:/^GTM-[A-Z0-9]{4,12}$/'], 'help' => 'e.g. GTM-XXXXXXX. Use GTM or GA4, not both, to avoid double counting.'],
                'meta_pixel_id' => ['label' => 'Meta (Facebook) Pixel ID', 'rules' => ['nullable', 'regex:/^[0-9]{6,20}$/']],
                'robots_extra' => ['label' => 'Extra robots.txt rules', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000', 'help' => 'Added under User-agent: *, e.g. Disallow: /private'],
                'discourage_indexing' => ['label' => 'Hide the whole site from search engines', 'type' => 'checkbox', 'rules' => 'nullable|boolean', 'help' => 'Only for a staging copy. Adds noindex to every page.'],
            ],
        ],
    ],
];
