---
layout: default
title: Caching
---

# Caching

The `CachedDereferencer` will cache any loaded schemas and the final dereferenced schema.  This can make loading local schemas a little bit faster and loading remote schemas significantly faster.

## Usage

The `CachedDereferencer` accepts any cache that implements the [PSR-16 Simple Cache Interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md).  The following example uses [sabre/cache](https://github.com/fruux/sabre-cache).

```php
$cache        = new \Sabre\Cache\Apcu();
$dereferencer = new CachedDereferencer(new Dereferencer(), $cache);

$schema = $dereferencer->dereference('http://json-schema.org/draft-04/schema');
```
