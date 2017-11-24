<?php

namespace League\JsonReference\Test;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use League\JsonReference\CachedDereferencer;
use League\JsonReference\Dereferencer;
use function peterpostmann\uri\fileuri;

class CachedDereferencerTest extends \PHPUnit_Framework_TestCase
{
    function test_it_caches_the_schema_when_using_a_uri()
    {
        $cache  = new ArrayCachePool();
        $cache  = new SimpleCacheBridge($cache);
        $deref  = new CachedDereferencer(new Dereferencer(), $cache);
        $path   = fileuri('fixtures/inline-ref.json', __DIR__);
        $result = $deref->dereference($path);

        $this->assertSame($result, $cache->get(sha1($path)));
    }

    function test_it_caches_the_schema_when_using_an_object()
    {
        $cache  = new ArrayCachePool();
        $cache  = new SimpleCacheBridge($cache);
        $deref  = new CachedDereferencer(new Dereferencer(), $cache);
        $schema = json_decode(file_get_contents(__DIR__ . '/fixtures/inline-ref.json'));
        $result = $deref->dereference($schema);

        $this->assertSame($result, $cache->get(sha1(json_encode($schema))));
    }

    function test_it_caches_the_schema_when_using_an_object_and_specifying_the_uri()
    {
        $cache  = new ArrayCachePool();
        $cache  = new SimpleCacheBridge($cache);
        $deref  = new CachedDereferencer(new Dereferencer(), $cache);
        $schema = json_decode(file_get_contents(__DIR__ . '/fixtures/inline-ref.json'));
        $path   = fileuri('fixtures/inline-ref.json', __DIR__);
        $result = $deref->dereference($schema, $path);

        $this->assertSame($result, $cache->get(sha1($path)));
    }

    function test_it_uses_the_cached_schema()
    {
        $cache  = new ArrayCachePool();
        $cache  = new SimpleCacheBridge($cache);
        $deref  = new CachedDereferencer(new Dereferencer(), $cache);
        $cache->set(sha1($path = 'file://the-schema'), $schema = json_decode('{"hello": "world"}'));
        $result = $deref->dereference($path);
        $this->assertSame($schema, $result);
    }
}
