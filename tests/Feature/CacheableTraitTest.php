<?php

namespace MehulK\QueryOptimizer\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use MehulK\QueryOptimizer\Traits\CacheableTrait;
use Exception;

class CacheableTraitTest extends TestCase
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
        
        // Create test models that use the CacheableTrait
        $this->testModel = new class extends Model {
            use CacheableTrait;
            protected $table = 'test_models';
            protected $fillable = ['name', 'status'];
        };

        $this->relatedModel = new class extends Model {
            use CacheableTrait;
            protected $table = 'related_models';
            protected $fillable = ['test_model_id', 'name'];
        };
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function test_it_can_cache_model_find_results()
    {
        $modelData = ['id' => 1, 'name' => 'Test Model'];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($modelData);

        $result = $this->testModel->cachedFind(1);
        
        $this->assertEquals($modelData, $result);
    }

    public function test_it_can_cache_model_all_results()
    {
        $modelsData = [
            ['id' => 1, 'name' => 'Test Model 1'],
            ['id' => 2, 'name' => 'Test Model 2'],
        ];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($modelsData);

        $results = $this->testModel->cachedAll();
        
        $this->assertEquals($modelsData, $results);
    }

    public function test_it_can_clear_model_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('test_models_*');

        $this->testModel->clearModelCache();
    }

    public function test_it_can_cache_model_with_relationships()
    {
        $modelData = [
            'id' => 1,
            'name' => 'Test Model',
            'related_models' => [
                ['id' => 1, 'test_model_id' => 1, 'name' => 'Related 1'],
                ['id' => 2, 'test_model_id' => 1, 'name' => 'Related 2'],
            ]
        ];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($modelData);

        $result = $this->testModel->cachedFind(1, ['related_models']);
        
        $this->assertEquals($modelData, $result);
    }

    public function test_it_can_cache_model_with_scopes()
    {
        $modelsData = [
            ['id' => 1, 'name' => 'Active Model 1', 'status' => 'active'],
            ['id' => 2, 'name' => 'Active Model 2', 'status' => 'active'],
        ];
        
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($modelsData);

        $results = $this->testModel->cachedWhere('status', 'active');
        
        $this->assertEquals($modelsData, $results);
    }

    public function test_it_handles_model_not_found()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(null);

        $result = $this->testModel->cachedFind(999);
        
        $this->assertNull($result);
    }

    public function test_it_handles_empty_collection()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([]);

        $results = $this->testModel->cachedWhere('status', 'inactive');
        
        $this->assertEmpty($results);
    }

    public function test_it_respects_cache_ttl()
    {
        $modelData = ['id' => 1, 'name' => 'Test Model'];
        $ttl = 3600;
        
        Cache::shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $cacheTtl, $callback) use ($ttl) {
                return $cacheTtl === $ttl;
            })
            ->andReturn($modelData);

        $result = $this->testModel->cachedFind(1, [], $ttl);
        
        $this->assertEquals($modelData, $result);
    }

    public function test_it_handles_query_exceptions()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->expectException(Exception::class);
        
        $this->testModel->cachedFind(1, [], 0, function() {
            throw new Exception('Database error');
        });
    }
} 