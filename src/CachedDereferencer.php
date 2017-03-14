<?php

namespace League\JsonReference;

use Closure;
use League\JsonReference\Loaders\CachedLoader;
use Psr\SimpleCache\CacheInterface;

final class CachedDereferencer implements DereferencerInterface
{
    /**
     * @var Dereferencer
     */
    private $dereferencer;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param Dereferencer   $dereferencer
     * @param CacheInterface $cache
     * @param bool           $cacheLoaders
     */
    public function __construct(Dereferencer $dereferencer, CacheInterface $cache, $cacheLoaders = true)
    {
        $this->dereferencer = $dereferencer;
        $this->cache        = $cache;

        if ($cacheLoaders) {
            $this->cacheLoaders();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dereference($schema, $uri = '')
    {
        $key = static::key($schema, $uri);

        return $this->remember($key, function () use ($schema, $uri) {
            return $this->dereferencer->dereference($schema, $uri);
        });
    }

    /**
     * @param string  $key
     * @param Closure $callback
     *
     * @return object
     */
    private function remember($key, Closure $callback)
    {
        $value = $this->cache->get($key);

        if ($value !== null) {
            return $value;
        }

        $this->cache->set($key, $value = $callback());

        return $value;
    }

    /**
     * @param string|object $schema
     * @param string        $uri
     *
     * @return string
     */
    private static function key($schema, $uri)
    {
        if ($uri !== '') {
            return sha1($uri);
        }

        return sha1(is_string($schema) ? $schema : json_encode($schema));
    }

    /**
     * @return void
     */
    private function cacheLoaders()
    {
        $loaderManager = $this->dereferencer->getLoaderManager();
        foreach ($loaderManager->getLoaders() as $prefix => $loader) {
            $loaderManager->registerLoader($prefix, new CachedLoader($this->cache, $loader));
        }
    }
}
