<?php

namespace Modules\OfferConversations\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\OfferConversations\Entities\OfferConversation;
use Modules\OfferConversations\Observers\OfferConversationObserver;

class OfferConversationsServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('OfferConversations', 'Database/Migrations'));

        //observer
        OfferConversation::observe(OfferConversationObserver::class);
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
            module_path('OfferConversations', 'Config/config.php') => config_path('offerconversations.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('OfferConversations', 'Config/config.php'), 'offerconversations'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/offerconversations');

        $sourcePath = module_path('OfferConversations', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/offerconversations';
        }, \Config::get('view.paths')), [$sourcePath]), 'offerconversations');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/offerconversations');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'offerconversations');
        } else {
            $this->loadTranslationsFrom(module_path('OfferConversations', 'Resources/lang'), 'offerconversations');
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
            app(Factory::class)->load(module_path('OfferConversations', 'Database/factories'));
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
