<?php
namespace MehulK\QueryOptimizer\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCache extends Command
{
    protected $signature = 'queryoptimizer:clear-cache';
    protected $description = 'Clear the query optimizer cache';

    public function handle()
    {
        Cache::flush();
        $this->info('Query optimizer cache cleared.');
    }
}
