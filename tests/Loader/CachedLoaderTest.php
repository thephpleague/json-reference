<?php

namespace ActiveRules\JsonReference\Test\Loader;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use ActiveRules\JsonReference\Loader\ArrayLoader;
use ActiveRules\JsonReference\Loader\CachedLoader;

class CachedLoaderTest extends \PHPUnit_Framework_TestCase
{
    function test_it_caches_the_schema()
    {
        $cache  = new ArrayCachePool();
        $cache  = new SimpleCacheBridge($cache);
        $path   = 'file://schema';
        $schema = json_decode('{"hello": "world"}');
        $loader = new CachedLoader($cache, new \ActiveRules\JsonReference\Loader\ArrayLoader([$path => $schema]));
        $loader->load($path);
        $this->assertSame($schema, $cache->get(sha1($path)));
    }

    function test_it_uses_the_cached_schema()
    {
        $cache  = new ArrayCachePool();
        $cache  = new SimpleCacheBridge($cache);
        $loader = new CachedLoader($cache, new ArrayLoader([]));
        $cache->set(sha1($path = 'file://schema'), $schema = json_decode('{"hello": "world"}'));
        $result = $loader->load($path);
        $this->assertSame($schema, $result);
    }
}
