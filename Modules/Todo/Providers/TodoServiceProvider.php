<?php

namespace Modules\Todo\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Todo\Entities\Board;
use Modules\Todo\Observers\BoardObserver;
use Modules\Todo\Entities\Card;
use Modules\Todo\Observers\CardObserver;
use Modules\Todo\Entities\Task;
use Modules\Todo\Observers\TaskObserver;

class TodoServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('Todo', 'Database/Migrations'));
        Board::observe(BoardObserver::class);
        Card::observe(CardObserver::class);
        Task::observe(TaskObserver::class);

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
            module_path('Todo', 'Config/config.php') => config_path('todo.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Todo', 'Config/config.php'), 'todo'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/todo');

        $sourcePath = module_path('Todo', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/todo';
        }, \Config::get('view.paths')), [$sourcePath]), 'todo');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/todo');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'todo');
        } else {
            $this->loadTranslationsFrom(module_path('Todo', 'Resources/lang'), 'todo');
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
            app(Factory::class)->load(module_path('Todo', 'Database/factories'));
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
