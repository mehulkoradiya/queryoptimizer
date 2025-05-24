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

    public static function cachedAll()
    {
        $key = static::class.':all';
        return Cache::remember($key, config('queryoptimizer.cache_ttl', 3600), fn() => static::all()->toArray());
    }

    public static function cachedWhere($column, $value)
    {
        $key = static::class.':where:'.$column.':'.$value;
        return Cache::remember($key, config('queryoptimizer.cache_ttl', 3600), fn() => static::where($column, $value)->get()->toArray());
    }

    public static function clearModelCache()
    {
        $prefix = (new static)->getTable() . '_*';
        // This is a simple implementation; in production, consider using a cache store that supports tags or key listing
        Cache::forget($prefix);
    }
}