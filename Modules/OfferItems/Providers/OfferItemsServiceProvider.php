<?php

namespace Modules\OfferItems\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\OfferItems\Entities\OfferItems;
use Modules\OfferItems\Observers\OfferItemsObserver;

class OfferItemsServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('OfferItems', 'Database/Migrations'));
        OfferItems::observe(OfferItemsObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('OfferItems', 'Config/config.php') => config_path('offeritems.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('OfferItems', 'Config/config.php'), 'offeritems'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/offeritems');

        $sourcePath = module_path('OfferItems', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/offeritems';
        }, \Config::get('view.paths')), [$sourcePath]), 'offeritems');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/offeritems');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'offeritems');
        } else {
            $this->loadTranslationsFrom(module_path('OfferItems', 'Resources/lang'), 'offeritems');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('OfferItems', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
