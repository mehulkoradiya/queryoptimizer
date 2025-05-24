<?php
namespace MehulK\QueryOptimizer\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class QueryExecutedListener
{
    public function handle(QueryExecuted $event)
    {
        if (!config('queryoptimizer.enabled', true)) {
            return;
        }

        $query = $event->sql;
        $bindings = $event->bindings;
        $time = $event->time;

        // Log slow queries (taking more than 100ms)
        if ($time > 100) {
            Log::warning('Slow query detected', [
                'query' => $query,
                'bindings' => $bindings,
                'time' => $time,
                'connection' => $event->connectionName
            ]);
        }
    }
}