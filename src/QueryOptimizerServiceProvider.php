<?php
namespace MehulK\QueryOptimizer;

use Illuminate\Support\ServiceProvider;

class QueryOptimizerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/queryoptimizer.php', 'queryoptimizer');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/queryoptimizer.php' => config_path('queryoptimizer.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\AnalyzeQueries::class,
                Commands\ClearCache::class,
            ]);
        }

        // Attach event listener
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Database\Events\QueryExecuted::class,
            Listeners\QueryExecutedListener::class
        );
    }
}
