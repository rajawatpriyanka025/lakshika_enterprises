<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('image_path');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->string('canonical_url')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->boolean('noindex')->default(false)->after('og_image');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('image_alt')->nullable()->after('image_path');
            $table->json('marketplace_links')->nullable()->after('featured');
            $table->unsignedInteger('sort_order')->default(0)->after('marketplace_links');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->string('canonical_url')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->boolean('noindex')->default(false)->after('og_image');
        });

        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('intro');
            $table->json('sections')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('marketplaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('search_url');
            $table->string('search_parameter', 40)->default('q');
            $table->string('store_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->json('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_url');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('company')->nullable();
            $table->string('type', 20)->default('retail');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('city')->nullable();
            $table->string('enquiry_type', 20)->default('general');
            $table->unsignedInteger('quantity')->nullable();
            $table->text('message')->nullable();
            $table->string('source', 30)->default('contact_form')->index();
            $table->string('status', 20)->default('new')->index();
            $table->timestamp('follow_up_at')->nullable()->index();
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('whatsapp_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_clicks');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('marketplaces');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('guides');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_alt', 'marketplace_links', 'sort_order', 'is_active', 'canonical_url', 'og_image', 'noindex']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'sort_order', 'is_active', 'canonical_url', 'og_image', 'noindex']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
