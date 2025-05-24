<?php

namespace MehulK\QueryOptimizer\Tests\Unit;

use MehulK\QueryOptimizer\QueryOptimizer;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use Exception;

class QueryOptimizerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'MehulK\QueryOptimizer\QueryOptimizerServiceProvider',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->optimizer = new QueryOptimizer();
    }

    /** @test */
    public function it_can_cache_query_results()
    {
        $query = "SELECT * FROM users WHERE id = 1";
        $result = ['id' => 1, 'name' => 'Test User'];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($result);

        $cachedResult = $this->optimizer->cache($query, function() use ($result) {
            return $result;
        });

        $this->assertEquals($result, $cachedResult);
    }

    /** @test */
    public function it_can_clear_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('query_optimizer_*');

        $this->optimizer->clearCache();
    }

    /** @test */
    public function it_handles_cache_misses()
    {
        $query = "SELECT * FROM users WHERE id = 1";
        $result = ['id' => 1, 'name' => 'Test User'];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $cachedResult = $this->optimizer->cache($query, function() use ($result) {
            return $result;
        });

        $this->assertEquals($result, $cachedResult);
    }

    /** @test */
    public function it_respects_cache_expiration()
    {
        $query = "SELECT * FROM users WHERE id = 1";
        $result = ['id' => 1, 'name' => 'Test User'];
        $ttl = 3600;
        
        Cache::shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $cacheTtl, $callback) use ($ttl) {
                return $cacheTtl === $ttl;
            })
            ->andReturn($result);

        $cachedResult = $this->optimizer->cache($query, function() use ($result) {
            return $result;
        }, $ttl);

        $this->assertEquals($result, $cachedResult);
    }

    /** @test */
    public function it_handles_complex_queries()
    {
        $query = "SELECT u.*, p.* FROM users u 
                 JOIN posts p ON u.id = p.user_id 
                 WHERE u.status = 'active' 
                 AND p.published_at > NOW() - INTERVAL 7 DAY";
        $result = [
            ['id' => 1, 'name' => 'User 1', 'post_id' => 1, 'title' => 'Post 1'],
            ['id' => 2, 'name' => 'User 2', 'post_id' => 2, 'title' => 'Post 2'],
        ];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($result);

        $cachedResult = $this->optimizer->cache($query, function() use ($result) {
            return $result;
        });

        $this->assertEquals($result, $cachedResult);
    }

    /** @test */
    public function it_handles_query_execution_errors()
    {
        $query = "SELECT * FROM non_existent_table";
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->expectException(Exception::class);
        
        $this->optimizer->cache($query, function() {
            throw new Exception('Table does not exist');
        });
    }

    /** @test */
    public function it_handles_empty_results()
    {
        $query = "SELECT * FROM users WHERE id = 999";
        $result = [];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($result);

        $cachedResult = $this->optimizer->cache($query, function() use ($result) {
            return $result;
        });

        $this->assertEquals($result, $cachedResult);
    }

    /** @test */
    public function it_handles_null_results()
    {
        $query = "SELECT * FROM users WHERE id = 999";
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(null);

        $cachedResult = $this->optimizer->cache($query, function() {
            return null;
        });

        $this->assertNull($cachedResult);
    }
} 