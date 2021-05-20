<?php

namespace Modules\TicketDialog\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\TicketDialog\Entities\Comment;
use Modules\TicketDialog\Entities\Reply;
use Modules\TicketDialog\Entities\SubReply;
use Modules\TicketDialog\Observers\CommentObserver;
use Modules\TicketDialog\Observers\ReplyObserver;
use Modules\TicketDialog\Observers\SubReplyObserver;

class TicketDialogServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('TicketDialog', 'Database/Migrations'));
        Reply::observe(ReplyObserver::class);
        SubReply::observe(SubReplyObserver::class);
        Comment::observe(CommentObserver::class);
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
            module_path('TicketDialog', 'Config/config.php') => config_path('ticketdialog.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('TicketDialog', 'Config/config.php'), 'ticketdialog'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/ticketdialog');

        $sourcePath = module_path('TicketDialog', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/ticketdialog';
        }, \Config::get('view.paths')), [$sourcePath]), 'ticketdialog');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/ticketdialog');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'ticketdialog');
        } else {
            $this->loadTranslationsFrom(module_path('TicketDialog', 'Resources/lang'), 'ticketdialog');
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
            app(Factory::class)->load(module_path('TicketDialog', 'Database/factories'));
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
