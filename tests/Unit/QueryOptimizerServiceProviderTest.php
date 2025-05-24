<?php

namespace MehulK\QueryOptimizer\Tests\Unit;

use Orchestra\Testbench\TestCase;
use MehulK\QueryOptimizer\QueryOptimizerServiceProvider;
use MehulK\QueryOptimizer\QueryOptimizer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class QueryOptimizerServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            QueryOptimizerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queryoptimizer', [
            'default_ttl' => 3600,
            'cache_prefix' => 'query_optimizer_',
            'enabled' => true,
        ]);
    }

    public function test_it_registers_the_service_provider()
    {
        $this->assertTrue($this->app->bound('query-optimizer'));
    }

    public function test_it_publishes_config_file()
    {
        $this->artisan('vendor:publish', [
            '--provider' => QueryOptimizerServiceProvider::class,
            '--tag' => 'config'
        ]);

        $this->assertFileExists(config_path('queryoptimizer.php'));
    }

    public function test_it_registers_singleton_instance()
    {
        $instance1 = $this->app->make('query-optimizer');
        $instance2 = $this->app->make('query-optimizer');

        $this->assertInstanceOf(QueryOptimizer::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    public function test_it_loads_default_configuration()
    {
        $config = $this->app['config']->get('queryoptimizer');

        $this->assertEquals(3600, $config['default_ttl']);
        $this->assertEquals('query_optimizer_', $config['cache_prefix']);
        $this->assertTrue($config['enabled']);
    }

    public function test_it_allows_config_override()
    {
        Config::set('queryoptimizer.default_ttl', 7200);
        Config::set('queryoptimizer.cache_prefix', 'custom_prefix_');

        $config = $this->app['config']->get('queryoptimizer');

        $this->assertEquals(7200, $config['default_ttl']);
        $this->assertEquals('custom_prefix_', $config['cache_prefix']);
    }

    public function test_it_registers_commands()
    {
        $commands = Artisan::all();
        
        $this->assertArrayHasKey('queryoptimizer:analyze', $commands);
        $this->assertArrayHasKey('queryoptimizer:clear-cache', $commands);
    }

    public function test_it_merges_config_with_package_defaults()
    {
        $this->artisan('vendor:publish', [
            '--provider' => QueryOptimizerServiceProvider::class,
            '--tag' => 'config'
        ]);

        $config = $this->app['config']->get('queryoptimizer');
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('default_ttl', $config);
        $this->assertArrayHasKey('cache_prefix', $config);
        $this->assertArrayHasKey('enabled', $config);
    }
} 