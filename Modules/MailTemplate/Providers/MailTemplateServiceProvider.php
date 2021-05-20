<?php

namespace Modules\MailTemplate\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\MailTemplate\Entities\MailTemplate;
use Modules\MailTemplate\Entities\MailTemplateCategory;
use Modules\MailTemplate\Observers\MailTemplateObserver;
use Modules\MailTemplate\Observers\MailTemplateCategoryObserver;

class MailTemplateServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('MailTemplate', 'Database/Migrations'));
        MailTemplate::observe(MailTemplateObserver::class);
        MailTemplateCategory::observe(MailTemplateCategoryObserver::class);
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
            module_path('MailTemplate', 'Config/config.php') => config_path('mailtemplate.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('MailTemplate', 'Config/config.php'), 'mailtemplate'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/mailtemplate');

        $sourcePath = module_path('MailTemplate', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/mailtemplate';
        }, \Config::get('view.paths')), [$sourcePath]), 'mailtemplate');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/mailtemplate');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'mailtemplate');
        } else {
            $this->loadTranslationsFrom(module_path('MailTemplate', 'Resources/lang'), 'mailtemplate');
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
            app(Factory::class)->load(module_path('MailTemplate', 'Database/factories'));
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
