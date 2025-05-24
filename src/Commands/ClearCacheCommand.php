<?php

namespace MehulK\QueryOptimizer\Commands;

use Illuminate\Console\Command;
use MehulK\QueryOptimizer\QueryOptimizer;

class ClearCacheCommand extends Command
{
    protected $signature = 'queryoptimizer:clear-cache';
    protected $description = 'Clear all cached queries';

    public function handle(QueryOptimizer $optimizer)
    {
        $optimizer->clearCache();
        $this->info('Query cache cleared successfully.');
    }
} 