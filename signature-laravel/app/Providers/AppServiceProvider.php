<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Use document root as public path so "php artisan storage:link" creates the link
        // at document_root/storage (where the web server serves from).
        $documentRoot = base_path('../');
        if (file_exists($documentRoot.'index.php') && is_dir($documentRoot)) {
            $this->app->usePublicPath(realpath($documentRoot) ?: $documentRoot);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure asset() and storage URLs use APP_URL (fixes images when behind proxy or wrong host)
        $appUrl = config('app.url');
        if (!empty($appUrl)) {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // Use Bootstrap 5 pagination
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        $settings = null;
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::first();
            }
        } catch (\Throwable $e) {
            // If DB is unavailable or settings table missing, continue with null (e.g. during install)
        }
        View::share('appSettings', $settings);
        View::share('defaultAppIcon', asset('images/sifavicon.png'));

        // Add fallback view location (used to resolve missing/mismatched Blade files
        // for modules like Contract / PI / PO in this workspace).
        // This ensures view('proforma-invoices.create') loads the real Blade template
        // from the working reference workspace when it is not available here.
        $fallbackViewsPath = '/home/signature-in-house-test/htdocs/test.signature-in-house.com/signature-laravel/resources/views';
        if (is_dir($fallbackViewsPath)) {
            View::addLocation($fallbackViewsPath);
        }

        // Explicit route model binding for damage details
        \Illuminate\Support\Facades\Route::bind('damageDetail', function ($value) {
            return \App\Models\DamageDetail::findOrFail($value);
        });
        
        // Explicit route model binding for damage images
        \Illuminate\Support\Facades\Route::bind('damageImage', function ($value) {
            return \App\Models\DamageImage::findOrFail($value);
        });
    }
}
