# Laravel Query Optimizer

This package provides intelligent query caching and optimization tools for Laravel.

## Installation
```bash
composer require MehulK/query-optimizer
php artisan vendor:publish --tag=config
```

## Usage
- Add `CacheableTrait` to your Eloquent models:
```php
use MehulK\QueryOptimizer\Traits\CacheableTrait;

class Post extends Model {
    use CacheableTrait;
}
```
- Cache queries:
```php
$post = Post::cachedFind(1);
```
- Run analysis:
```bash
php artisan queryoptimizer:analyze
```
- Clear cache:
```bash
php artisan queryoptimizer:clear-cache
```

## Configuration
Check `config/queryoptimizer.php` for options.
