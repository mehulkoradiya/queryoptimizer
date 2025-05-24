<?php
namespace MehulK\QueryOptimizer\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheableTrait
{
    public static function cachedFind($id)
    {
        $key = static::class.':find:'.$id;
        return Cache::remember($key, config('queryoptimizer.cache_ttl'), fn() => static::find($id));
    }
}