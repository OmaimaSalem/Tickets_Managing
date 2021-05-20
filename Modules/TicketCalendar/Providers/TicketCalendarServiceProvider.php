<?php

namespace Modules\TicketCalendar\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class TicketCalendarServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('TicketCalendar', 'Database/Migrations'));
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
            module_path('TicketCalendar', 'Config/config.php') => config_path('ticketcalendar.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('TicketCalendar', 'Config/config.php'), 'ticketcalendar'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/ticketcalendar');

        $sourcePath = module_path('TicketCalendar', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/ticketcalendar';
        }, \Config::get('view.paths')), [$sourcePath]), 'ticketcalendar');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/ticketcalendar');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'ticketcalendar');
        } else {
            $this->loadTranslationsFrom(module_path('TicketCalendar', 'Resources/lang'), 'ticketcalendar');
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
            app(Factory::class)->load(module_path('TicketCalendar', 'Database/factories'));
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
