<?php

namespace MehulK\QueryOptimizer;

use Illuminate\Support\Facades\Cache;

class QueryOptimizer
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function cache($query, $callback, $ttl = null)
    {
        $ttl = $ttl ?? $this->config['default_ttl'] ?? 3600;
        $prefix = $this->config['cache_prefix'] ?? 'query_optimizer_';
        
        return Cache::remember(
            $prefix . md5($query),
            $ttl,
            $callback
        );
    }

    public function clearCache()
    {
        $prefix = $this->config['cache_prefix'] ?? 'query_optimizer_';
        Cache::forget($prefix . '*');
    }
} 