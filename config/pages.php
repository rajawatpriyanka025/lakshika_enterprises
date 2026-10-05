<?php

/*
| Editable copy for the fixed site pages (Admin → Pages).
| In headings, wrap words in *asterisks* for italics and press Enter for a line break.
| In long text, leave a blank line between paragraphs.
*/

return [
    'home' => [
        'name' => 'Home',
        'meta_title' => 'Wall Clocks for Every Room | Lakshika Enterprises',
        'meta_description' => 'Explore wall clock styles for living rooms, bedrooms, kitchens and more. Discover the Lakshika Enterprises collection and browse buying guides.',
        'fields' => [
            'hero_eyebrow' => ['label' => 'Hero label', 'default' => 'THE ART OF EVERYDAY TIME'],
            'hero_heading' => ['label' => 'Hero heading (H1)', 'type' => 'heading', 'default' => "Make every\nminute *feel at home.*"],
            'hero_text' => ['label' => 'Hero text', 'type' => 'textarea', 'default' => 'Find a wall clock that feels like it belongs. Explore styles for slow mornings, shared meals and all the little moments in between.'],
            'hero_button' => ['label' => 'Hero button', 'default' => 'Explore the collection'],
            'intro_label' => ['label' => 'Intro strip label', 'default' => 'GOOD DESIGN, RIGHT ON TIME'],
            'intro_text' => ['label' => 'Intro strip text', 'type' => 'heading', 'default' => 'Thoughtful wall clock styles to make your everyday spaces feel a little more *you.*'],
            'collections_eyebrow' => ['label' => 'Collections label', 'default' => 'FIND YOUR KIND OF TIME'],
            'collections_heading' => ['label' => 'Collections heading', 'default' => 'A clock for every corner.'],
            'feature_eyebrow' => ['label' => 'Feature band label', 'default' => 'A LITTLE HELP, BEFORE YOU HANG IT'],
            'feature_heading' => ['label' => 'Feature band heading', 'type' => 'heading', 'default' => "Good time starts\nwith the right *spot.*"],
            'feature_text' => ['label' => 'Feature band text', 'type' => 'textarea', 'default' => 'Size, placement, readability — a few small choices make finding your wall clock feel a whole lot easier.'],
            'products_eyebrow' => ['label' => 'Featured products label', 'default' => 'A FEW PLACES TO BEGIN'],
            'products_heading' => ['label' => 'Featured products heading', 'default' => 'Meet the clock styles.'],
            'quote' => ['label' => 'Quote', 'type' => 'textarea', 'default' => 'A little detail can change the feeling of a whole room.'],
            'seo_eyebrow' => ['label' => 'SEO block label', 'default' => 'WALL CLOCKS, THOUGHTFULLY CHOSEN'],
            'seo_heading' => ['label' => 'SEO block heading (H2)', 'default' => 'Find a wall clock that feels right at home.'],
            'seo_text' => ['label' => 'SEO block text', 'type' => 'longtext', 'default' => "A wall clock can be a quiet everyday essential or the detail that brings a room together. Lakshika Enterprises shares wall clock styles and practical ideas to help you find a look that suits your home.\n\nExplore clocks for living rooms, bedrooms, kitchens and shared spaces. Our guides cover choosing a clock size, finding a comfortable viewing position and styling a clock with the things you already love."],
        ],
    ],

    'shop' => [
        'name' => 'Shop',
        'meta_title' => 'Shop All Products',
        'meta_description' => 'Browse the full Lakshika Enterprises collection for living rooms, bedrooms, kitchens and workspaces, and find a style that suits your space.',
        'fields' => [
            'hero_eyebrow' => ['label' => 'Hero label', 'default' => 'THE LAKSHIKA COLLECTION'],
            'hero_heading' => ['label' => 'Hero heading (H1)', 'type' => 'heading', 'default' => 'Made for the way *you live.*'],
            'hero_text' => ['label' => 'Hero text', 'type' => 'textarea', 'default' => 'Explore styles for the spaces you spend your days in. Browse by room, by collection or simply by what catches your eye.'],
        ],
    ],

    'guides' => [
        'name' => 'Guides',
        'meta_title' => 'Wall Clock Buying Guides',
        'meta_description' => 'Practical guides to choosing a wall clock, finding the right size and styling a clock in your home.',
        'fields' => [
            'hero_eyebrow' => ['label' => 'Hero label', 'default' => 'A LITTLE KNOW-HOW GOES A LONG WAY'],
            'hero_heading' => ['label' => 'Hero heading (H1)', 'type' => 'heading', 'default' => "Make room for\n*good timing.*"],
            'hero_text' => ['label' => 'Hero text', 'type' => 'textarea', 'default' => 'Simple, practical advice to help you choose a wall clock, find its place and make it feel right at home.'],
        ],
    ],

    'faqs' => [
        'name' => 'FAQs',
        'meta_title' => 'Wall Clock FAQs',
        'meta_description' => 'Answers to common questions about choosing, placing and finding a wall clock from Lakshika Enterprises.',
        'fields' => [
            'hero_eyebrow' => ['label' => 'Hero label', 'default' => 'A FEW THINGS YOU MIGHT BE WONDERING'],
            'hero_heading' => ['label' => 'Hero heading (H1)', 'type' => 'heading', 'default' => "Good questions.\n*Helpful answers.*"],
            'hero_text' => ['label' => 'Hero text', 'type' => 'textarea', 'default' => 'Quick, practical answers to common wall clock questions.'],
        ],
    ],

    'about' => [
        'name' => 'About',
        'meta_title' => 'About Lakshika Enterprises | Wall Clocks',
        'meta_description' => 'Get to know Lakshika Enterprises, an online seller of wall clocks available through popular Indian marketplaces.',
        'fields' => [
            'hero_eyebrow' => ['label' => 'Hero label', 'default' => 'A LITTLE ABOUT US'],
            'hero_heading' => ['label' => 'Hero heading (H1)', 'type' => 'heading', 'default' => "Time has a way\nof bringing us *home.*"],
            'hero_text' => ['label' => 'Hero text', 'type' => 'textarea', 'default' => 'We are Lakshika Enterprises. We sell wall clocks online and believe the everyday things we live with should feel considered, useful and right for the spaces we call home.'],
            'story_eyebrow' => ['label' => 'Story label', 'default' => 'THE WAY WE SEE IT'],
            'story_heading' => ['label' => 'Story heading', 'type' => 'heading', 'default' => "A familiar detail.\nA feeling all your own."],
            'story_text' => ['label' => 'Story text', 'type' => 'longtext', 'default' => "A wall clock marks the small moments that make up a day: a morning cup of tea, everyone around the table, the last few quiet minutes before bed. It can be practical and still add something lovely to a room.\n\nThrough Lakshika Enterprises, we make it easier to explore wall clock styles and find our product listings on the online marketplaces where we sell."],
            'cta_heading' => ['label' => 'Closing banner heading', 'default' => 'Useful can still feel beautiful.'],
            'cta_text' => ['label' => 'Closing banner text', 'type' => 'textarea', 'default' => 'Explore wall clocks for the rooms and routines that make a home yours.'],
        ],
    ],

    'contact' => [
        'name' => 'Contact',
        'meta_title' => 'Contact Lakshika Enterprises',
        'meta_description' => 'Send an enquiry to Lakshika Enterprises for product questions, bulk and dealer orders, or find our listings on Amazon, Flipkart and Meesho.',
        'fields' => [
            'hero_eyebrow' => ['label' => 'Hero label', 'default' => "WE'RE EASY TO FIND"],
            'hero_heading' => ['label' => 'Hero heading (H1)', 'type' => 'heading', 'default' => "Let's make time\nfor *the right details.*"],
            'hero_text' => ['label' => 'Hero text', 'type' => 'textarea', 'default' => 'Questions about a product, a bulk order or becoming a dealer? Message us on WhatsApp and we will get back to you quickly, or find our listings on your favourite marketplace.'],
            'form_heading' => ['label' => 'Enquiry heading', 'default' => 'Message us on WhatsApp.'],
            'form_text' => ['label' => 'Enquiry text', 'type' => 'textarea', 'default' => 'The quickest way to reach us. Pick a topic and your message is ready to send. For bulk, corporate gifting and dealer enquiries, add the quantity and your city.'],
            'marketplaces_heading' => ['label' => 'Marketplaces heading', 'default' => 'Find us on your favourite marketplace.'],
            'marketplaces_text' => ['label' => 'Marketplaces text', 'type' => 'textarea', 'default' => 'Marketplace product pages have the latest listing details, pricing, delivery information and seller contact options.'],
        ],
    ],
];
