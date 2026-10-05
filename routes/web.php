<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('shop');
Route::get('/collections/{category:slug}', [CatalogController::class, 'category'])->name('collections.show');
Route::get('/products/{product:slug}', [CatalogController::class, 'product'])->name('products.show');
Route::get('/guides', [PageController::class, 'guides'])->name('guides.index');
Route::get('/guides/{slug}', [PageController::class, 'guide'])->name('guides.show');
Route::get('/faqs', [PageController::class, 'faqs'])->name('faqs');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/whatsapp', WhatsappController::class)->middleware('throttle:30,1')->name('whatsapp');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/maintenance', MaintenanceController::class)->name('maintenance');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:admin-login');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('leads/export', [Admin\LeadController::class, 'export'])->name('leads.export');
        Route::post('leads/{lead}/notes', [Admin\LeadController::class, 'addNote'])->name('leads.notes.store');
        Route::resource('leads', Admin\LeadController::class)->except('edit');
        Route::resource('customers', Admin\CustomerController::class);
        Route::get('whatsapp-clicks', Admin\WhatsappClickController::class)->name('whatsapp-clicks.index');

        Route::get('product-seo', [Admin\ProductSeoController::class, 'index'])->name('product-seo.index');
        Route::put('product-seo', [Admin\ProductSeoController::class, 'update'])->name('product-seo.update');
        Route::post('product-seo/autofill', [Admin\ProductSeoController::class, 'autofill'])->name('product-seo.autofill');
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::resource('categories', Admin\CategoryController::class)->except('show');
        Route::resource('guides', Admin\GuideController::class)->except('show');
        Route::resource('faqs', Admin\FaqController::class)->except('show');
        Route::resource('marketplaces', Admin\MarketplaceController::class)->except('show');
        Route::resource('redirects', Admin\RedirectController::class)->except('show');

        Route::get('pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{key}', [Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{key}', [Admin\PageController::class, 'update'])->name('pages.update');

        Route::get('settings/{group?}', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings/{group}', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('account', [Admin\AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [Admin\AccountController::class, 'update'])->name('account.update');
    });
});
