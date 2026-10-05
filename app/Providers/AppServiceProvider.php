<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\Marketplace;
use App\Support\Seo;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(Seo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Live site: always build https:// links (canonical tags, sitemap, redirects), even behind a proxy.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));

        View::composer(['layouts.app', 'pages.contact', 'catalog.product'], function ($view) {
            $view->with('marketplaces', once(fn () => Marketplace::query()->active()->get()));
        });

        View::composer('admin.layouts.app', function ($view) {
            $view->with('newLeadCount', once(fn () => Lead::query()->where('status', 'new')->count()));
        });
    }
}
