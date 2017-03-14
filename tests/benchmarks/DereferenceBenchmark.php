<?php

namespace League\JsonReference\Bench;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use League\JsonReference\CachedDereferencer;
use League\JsonReference\Dereferencer;

/**
 * @Groups({"dereference"})
 * @Warmup(2)
 * @Revs(1000)
 */
abstract class DereferenceBenchmark extends Benchmark
{
    protected $schema;

    protected $arrayCache;

    abstract public function getSchema();

    public function setUp()
    {
        $this->schema = $this->getSchema();

        $this->arrayCache  = new SimpleCacheBridge(new ArrayCachePool());
    }

    public function benchStandard()
    {
        $schema = $this->schema;
        (new Dereferencer())->dereference($schema);
    }

    public function benchArrayCache()
    {
        $schema = $this->schema;
        (new CachedDereferencer(new Dereferencer(), $this->arrayCache))->dereference($schema);
    }
}
