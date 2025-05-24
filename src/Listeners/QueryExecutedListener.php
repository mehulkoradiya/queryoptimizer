<?php
namespace MehulK\QueryOptimizer\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class QueryExecutedListener
{
    public function handle(QueryExecuted $event)
    {
        if ($event->time > config('queryoptimizer.slow_query_threshold_ms')) {
            Log::warning('Slow query detected: '.$event->sql.' [Time: '.$event->time.' ms]');
        }
    }
}