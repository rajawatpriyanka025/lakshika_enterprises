# Lakshika Enterprises storefront

A Laravel 12 storefront for discovering Lakshika Enterprises wall clocks and finding marketplace listings. The site is server-rendered and includes individual product and collection pages, wall-clock buying guides, FAQs, metadata, structured data, and an XML sitemap.

## Local setup

Requirements: PHP 8.2+, Composer, and the PHP SQLite extension.

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000`. The default local database is SQLite. To use MySQL, set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` before running migrations.

## Catalog content

The seeded category and product copy provides a working storefront example; replace it with Lakshika Enterprises' actual product names, descriptions, and verified product details before publishing. The current examples intentionally do not claim prices, stock, dimensions, materials, delivery terms, or product certifications. To add product photography, place image files on Laravel's public storage disk, run `php artisan storage:link`, and set each product's `image_path` to its path relative to `storage/app/public`.

Categories and product descriptions are seeded in `database/seeders/DatabaseSeeder.php`. The public catalog is read from the `categories` and `products` tables. Add or edit entries there, then run `php artisan db:seed` to apply the seed content.

Marketplace buttons currently link to Amazon, Flipkart, and Meesho search results for the Lakshika Enterprises brand and product name. Confirm the exact seller listings and use their direct URLs before promoting them. Current marketplace pricing and availability are intentionally shown on those external listings, not invented on this site.

Set `APP_URL` in `.env` to the site's production HTTPS URL before generating the sitemap for search engines. Submit `/sitemap.xml` in Google Search Console after launch.

## Checks

```powershell
php artisan test
```
