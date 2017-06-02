<?php

namespace League\JsonReference\Bench;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Adapter\Predis\PredisCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use League\JsonReference\CachedDereferencer;
use League\JsonReference\Dereferencer;
use Predis\Client;

/**
 * @Groups({"dereference"})
 * @Warmup(2)
 * @Revs(1000)
 */
abstract class DereferenceBenchmark extends Benchmark
{
    protected $schema;

    protected $arrayCache;

    protected $redisCache;

    abstract public function getSchema();

    public function setUp()
    {
        $this->schema = $this->getSchema();

        $this->arrayCache  = new SimpleCacheBridge(new ArrayCachePool());

        $this->redisCache = new SimpleCacheBridge(new PredisCachePool($predis = new Client()));

        $predis->connect();
    }

    public function benchStandard()
    {
        $schema = $this->schema;
        Dereferencer::draft4()->dereference($schema);
    }

    public function benchArrayCache()
    {
        $schema = $this->schema;
        (new CachedDereferencer(Dereferencer::draft4(), $this->arrayCache))->dereference($schema);
    }

    public function benchRedisCache()
    {
        $schema = $this->schema;
        (new CachedDereferencer(Dereferencer::draft4(), $this->redisCache))->dereference($schema);
    }
}
