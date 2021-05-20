<?php

namespace Modules\ContractItems\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\ContractItems\Entities\ContractItems;
use Modules\ContractItems\Observers\ContractItemsObserver;

class ContractItemsServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('ContractItems', 'Database/Migrations'));
        ContractItems::observe(ContractItemsObserver::class);
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
            module_path('ContractItems', 'Config/config.php') => config_path('contractitems.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('ContractItems', 'Config/config.php'), 'contractitems'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/contractitems');

        $sourcePath = module_path('ContractItems', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/contractitems';
        }, \Config::get('view.paths')), [$sourcePath]), 'contractitems');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/contractitems');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'contractitems');
        } else {
            $this->loadTranslationsFrom(module_path('ContractItems', 'Resources/lang'), 'contractitems');
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
            app(Factory::class)->load(module_path('ContractItems', 'Database/factories'));
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
