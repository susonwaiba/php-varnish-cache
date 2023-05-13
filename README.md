# Php Varnish Cache

Varnish cache integration for PHP


Version: Under Development `dev-main`

Some stats:-

| Stack | Interval | Response Time |
| --- | --- | --- |
| Nginx + PHP-FPM | 1st | 7ms |
| Nginx + PHP-FPM | 2nd | 7ms |
| Nginx + PHP-FPM | 3nd | 7ms |
| Varnish + Nginx + PHP-FPM | 1st | 7ms |
| Varnish + Nginx + PHP-FPM | 2nd | 1ms |
| Varnish + Nginx + PHP-FPM | 3nd | 1ms |

## Quick Code Look

```php
<?php

use SusonWaiba\PhpVarnishCache\VarnishCacheManager;

// Direct use via Manager
$manager = new VarnishCacheManager();
$response = $manager->getVarnishCache()->clean();

// Set base_url and options
$varnishCache = $manager->setOptions([
    'base_url' => 'http//varnish',
    'timeout' => 2.0,
])->getVarnishCache(true);
$response = $varnishCache->clean();
```

## Installation

```bash
composer require ***/***
```

## Varnish

- `docker/varnish.vcl`

#### Varnish cache hit status

- Header key for cache debug: `X-Varnish-Cache-Debug`

#### Varnish cache headers

- Header key for cache tag: `X-Varnish-Tag`
- Header key for cache pool: `X-Varnish-Pool`

#### Varnish cache PURGE headers

- `PURGE` request with header `X-Varnish-Tag-Pattern` for tags
- `PURGE` request with header `X-Varnish-Pool-Pattern` for pool

## Development Setup

```bash
git clone https://github.com/susonwaiba/php-varnish-cache.git
cd php-varnish-cache

docker-compose build
docker-compose up -d

docker-compose exec fpm bash

bin/php-varnish-cache
bin/php-varnish-cache cache:clean
```

## Http Request

#### Request via Nginx

- http://localhost:8080

#### Request via Varnish

- http://localhost

#### Varnish cache clean

- http://localhost:8080/clean
- http://localhost/clean

## Running Tests

```bash
./vendor/bin/phpunit ./tests/
```

## Project Overview

- [x] Setup docker development environment
- [x] Enable varnish cache for requests
- [x] Varnish cache clean by tags
- [x] Varnish cache clean by pools
- [ ] Integration with Symfony
- [ ] Integration with Laravel
- [x] Command for cache clean
