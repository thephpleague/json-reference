<?php

namespace League\JsonReference\Loaders;

use League\JsonReference\Loader;
use Psr\SimpleCache\CacheInterface;

class CachedLoader implements Loader
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Loader
     */
    private $loader;

    /**
     * @param CacheInterface $cache
     * @param Loader         $loader
     */
    public function __construct(CacheInterface $cache, Loader $loader)
    {
        $this->cache  = $cache;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $key   = sha1($path);
        $value = $this->cache->get($key);

        if ($value !== null) {
            return $value;
        }

        $this->cache->set($key, $value = $this->loader->load($path));

        return $value;
    }
}
