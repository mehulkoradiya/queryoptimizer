<?php

namespace MehulK\QueryOptimizer\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyzeCommand extends Command
{
    protected $signature = 'queryoptimizer:analyze';
    protected $description = 'Analyze query performance and cache usage';

    public function handle()
    {
        $this->info('Analyzing query performance...');
        
        // Get all cached queries
        $prefix = config('queryoptimizer.cache_prefix', 'query_optimizer_');
        $keys = Cache::getStore()->many([$prefix . '*']);
        
        $this->table(
            ['Query', 'Cache Key', 'TTL'],
            collect($keys)->map(function ($value, $key) {
                return [
                    'query' => $value['query'] ?? 'Unknown',
                    'key' => $key,
                    'ttl' => $value['ttl'] ?? 'Unknown'
                ];
            })
        );
        
        $this->info('Analysis complete.');
    }
} 