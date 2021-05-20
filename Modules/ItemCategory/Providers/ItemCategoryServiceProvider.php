<?php

namespace Modules\ItemCategory\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\ItemCategory\Entities\ItemCategory;
use Modules\ItemCategory\Observers\ItemCategoryObserver;


class ItemCategoryServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('ItemCategory', 'Database/Migrations'));
        ItemCategory::observe(ItemCategoryObserver::class);
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
            module_path('ItemCategory', 'Config/config.php') => config_path('itemcategory.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('ItemCategory', 'Config/config.php'), 'itemcategory'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/itemcategory');

        $sourcePath = module_path('ItemCategory', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/itemcategory';
        }, \Config::get('view.paths')), [$sourcePath]), 'itemcategory');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/itemcategory');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'itemcategory');
        } else {
            $this->loadTranslationsFrom(module_path('ItemCategory', 'Resources/lang'), 'itemcategory');
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
            app(Factory::class)->load(module_path('ItemCategory', 'Database/factories'));
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
