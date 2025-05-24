<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cache TTL
    |--------------------------------------------------------------------------
    |
    | The default time-to-live for cached queries in seconds.
    |
    */
    'default_ttl' => env('QUERY_OPTIMIZER_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Cache Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix used for all cached queries.
    |
    */
    'cache_prefix' => env('QUERY_OPTIMIZER_PREFIX', 'query_optimizer_'),

    /*
    |--------------------------------------------------------------------------
    | Enable Query Optimization
    |--------------------------------------------------------------------------
    |
    | Enable or disable the query optimization functionality.
    |
    */
    'enabled' => env('QUERY_OPTIMIZER_ENABLED', true),
];
