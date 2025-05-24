<?php
namespace MehulK\QueryOptimizer;

use Illuminate\Support\ServiceProvider;

class QueryOptimizerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/queryoptimizer.php', 'queryoptimizer'
        );

        $this->app->singleton('query-optimizer', function ($app) {
            return new QueryOptimizer($app['config']->get('queryoptimizer'));
        });

        $this->app->alias('query-optimizer', QueryOptimizer::class);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/queryoptimizer.php' => config_path('queryoptimizer.php'),
            ], 'config');

            $this->commands([
                Commands\AnalyzeCommand::class,
                Commands\ClearCacheCommand::class,
            ]);
        }

        // Attach event listener
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Database\Events\QueryExecuted::class,
            Listeners\QueryExecutedListener::class
        );
    }
}
