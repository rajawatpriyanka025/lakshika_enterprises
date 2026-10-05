<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Modern Wall Clocks',
                'slug' => 'modern-wall-clocks',
                'description' => 'Explore modern wall clock styles for contemporary homes, from clean, understated designs to statement-making faces.',
                'meta_title' => 'Modern Wall Clocks for Contemporary Homes',
                'meta_description' => 'Browse modern wall clock styles for contemporary living rooms, bedrooms and workspaces.',
            ],
            [
                'name' => 'Decorative Wall Clocks',
                'slug' => 'decorative-wall-clocks',
                'description' => 'Discover decorative wall clock ideas that bring a little more character to your walls while keeping time easy to read.',
                'meta_title' => 'Decorative Wall Clocks for Your Home',
                'meta_description' => 'Find decorative wall clock ideas for living rooms, bedrooms and thoughtful home refreshes.',
            ],
            [
                'name' => 'Kitchen Wall Clocks',
                'slug' => 'kitchen-wall-clocks',
                'description' => 'Find kitchen wall clock styles that are easy to read at a glance and suit the feel of your everyday space.',
                'meta_title' => 'Kitchen Wall Clocks: Styles for Everyday Spaces',
                'meta_description' => 'Explore readable, practical wall clock styles and placement tips for kitchens and dining spaces.',
            ],
            [
                'name' => 'Living Room Wall Clocks',
                'slug' => 'living-room-wall-clocks',
                'description' => 'Explore wall clock styles and placement ideas for living rooms, from quiet accents to a focal point above a console.',
                'meta_title' => 'Living Room Wall Clocks & Styling Ideas',
                'meta_description' => 'Browse living room wall clock styles and tips for choosing a size, finish and placement.',
            ],
        ];

        foreach ($categories as $attributes) {
            Category::query()->firstOrCreate(['slug' => $attributes['slug']], $attributes);
        }

        $products = [
            [
                'category' => 'modern-wall-clocks',
                'name' => 'Minimal Round Wall Clock',
                'slug' => 'minimal-round-wall-clock',
                'excerpt' => 'A clean round silhouette to complement a calm, modern room.',
                'description' => 'A simple round wall clock can bring a practical finishing touch to a room without competing with its other details. Consider a clear dial, balanced proportions and a finish that works with your existing decor.',
                'featured' => true,
            ],
            [
                'category' => 'decorative-wall-clocks',
                'name' => 'Statement Decorative Wall Clock',
                'slug' => 'statement-decorative-wall-clock',
                'excerpt' => 'A decorative clock idea for a wall that needs a little more character.',
                'description' => 'A statement wall clock can work as both a timepiece and a focal point. Before choosing one, measure the wall and think about how its shape and finish will relate to nearby furniture and artwork.',
                'featured' => true,
            ],
            [
                'category' => 'kitchen-wall-clocks',
                'name' => 'Easy-Read Kitchen Wall Clock',
                'slug' => 'easy-read-kitchen-wall-clock',
                'excerpt' => 'A practical style to help you check the time while cooking.',
                'description' => 'In a kitchen, visibility matters. Look for a clock face that is easy to read from across the room and choose a position away from heat, splashes and heavy steam.',
                'featured' => true,
            ],
            [
                'category' => 'living-room-wall-clocks',
                'name' => 'Classic Living Room Wall Clock',
                'slug' => 'classic-living-room-wall-clock',
                'excerpt' => 'A versatile wall clock idea for a welcoming shared space.',
                'description' => 'A living room clock can sit above a console, on an open wall or among a considered gallery arrangement. Match its visual weight to the furniture below and leave enough breathing room around the dial.',
                'featured' => true,
            ],
            [
                'category' => 'modern-wall-clocks',
                'name' => 'Contemporary Statement Clock',
                'slug' => 'contemporary-statement-clock',
                'excerpt' => 'A bolder profile for a room that could use a defined focal point.',
                'description' => 'A larger or more expressive clock can anchor a plain wall. Check the available wall area first, then keep nearby frames and decor simple enough that the clock remains easy to see.',
                'featured' => false,
            ],
            [
                'category' => 'decorative-wall-clocks',
                'name' => 'Decorative Home Wall Clock',
                'slug' => 'decorative-home-wall-clock',
                'excerpt' => 'A considered accent for bedrooms, hallways and living spaces.',
                'description' => 'A decorative clock can add personality to an entryway, bedroom or shared living area. Choose a location with comfortable viewing height and enough contrast for the hands to stand out.',
                'featured' => false,
            ],
        ];

        foreach ($products as $attributes) {
            $category = Category::query()->where('slug', $attributes['category'])->firstOrFail();
            unset($attributes['category']);
            $attributes['category_id'] = $category->id;
            $attributes['meta_title'] = $attributes['name'].' | Lakshika Enterprises';
            $attributes['meta_description'] = $attributes['excerpt'].' Explore wall clock ideas from Lakshika Enterprises.';

            Product::query()->firstOrCreate(['slug' => $attributes['slug']], $attributes);
        }

        $this->call(ContentSeeder::class);
    }
}
