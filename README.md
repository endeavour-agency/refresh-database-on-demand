# Refresh Database On Demand
This package offers a drop-in replacement for Laravel's [RefreshDatabase](https://laravel.com/docs/11.x/database-testing#resetting-the-database-after-each-test) trait.

Laravel's default `RefreshDatabase` trait always performs a `migrate:fresh` at the start of running a single or multiple tests, even when migrations haven't been added or deleted.

When using this package's `RefreshDatabaseOnDemand` trait instead, `migrate:fresh` will only be called when new migrations have been added or migrations have been deleted. This significantly reduces start-up time of running a single or a set of tests.

## Installation
To install the package, simply require it with composer:
```shell
composer require endeavour-agency/refresh-database-on-demand
```

## Usage
To start using the power of on-demand database refreshing, replace any occurrence of `Illuminate\Foundation\Testing\RefreshDatabase` with `EndeavourAgency\RefreshDatabaseOnDemand\Traits\RefreshDatabaseOnDemand`.


**Before:**

```php
<?php
 
namespace Tests\Unit;
 
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
 
class ExampleTest extends TestCase
{
    use RefreshDatabase;
    
    ...
}
```

**After:**

```php
<?php
 
namespace Tests\Unit;
 
use EndeavourAgency\RefreshDatabaseOnDemand\Traits\RefreshDatabaseOnDemand;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
 
class ExampleTest extends TestCase
{
    use RefreshDatabaseOnDemand;
    
    ...
}
```
