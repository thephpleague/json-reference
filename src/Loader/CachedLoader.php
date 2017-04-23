<?php

namespace League\JsonReference\Loader;

use League\JsonReference\LoaderInterface;
use Psr\SimpleCache\CacheInterface;

final class CachedLoader implements LoaderInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @param CacheInterface  $cache
     * @param LoaderInterface $loader
     */
    public function __construct(CacheInterface $cache, LoaderInterface $loader)
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
