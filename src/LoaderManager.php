<?php

namespace League\JsonReference;

use League\JsonReference\DecoderManager;
use League\JsonReference\Loader\CurlWebLoader;
use League\JsonReference\Loader\FileGetContentsWebLoader;
use League\JsonReference\Loader\FileLoader;

final class LoaderManager
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];
    
    /**
     * @var DecoderManager
     */
    private $decoderManager;

    /**
     * @param LoaderInterface[] $loaders
     * @param DecoderManager $decoderManager
     */
    public function __construct(array $loaders = [], DecoderManager $decoderManager = null)
    {
        $this->decoderManager = $decoderManager ?: new DecoderManager([], 'json');
        
        if (empty($loaders)) {
            $this->registerDefaultFileLoader();
            $this->registerDefaultWebLoaders();
        }
        
        foreach ($loaders as $prefix => $loader) {
            $this->registerLoader($prefix, $loader);
        }
    }

    /**
     * Register a LoaderInterface for the given prefix.
     *
     * @param string          $prefix
     * @param LoaderInterface $loader
     */
    public function registerLoader($prefix, LoaderInterface $loader)
    {
        $this->loaders[$prefix] = $loader;
    }

    /**
     * Get all registered loaders, keyed by the prefix they are registered to load schemas for.
     *
     * @return LoaderInterface[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * Get the loader for the given prefix.
     *
     * @param string $prefix
     *
     * @return LoaderInterface
     * @throws \InvalidArgumentException
     */
    public function getLoader($prefix)
    {
        if (!$this->hasLoader($prefix)) {
            throw new \InvalidArgumentException(sprintf('A loader is not registered for the prefix "%s"', $prefix));
        }

        return $this->loaders[$prefix];
    }

    /**
     * @param string $prefix
     *
     * @return bool
     */
    public function hasLoader($prefix)
    {
        return isset($this->loaders[$prefix]);
    }

    /**
     * Register the default file loader.
     */
    private function registerDefaultFileLoader()
    {
        $this->loaders['file'] = new FileLoader($this->decoderManager);
    }

    /**
     * Register the default web loaders.  If the curl extension is loaded,
     * the CurlWebLoader will be used.  Otherwise the FileGetContentsWebLoader
     * will be used.  You can override this by registering your own loader
     * for the 'http' and 'https' protocols.
     */
    private function registerDefaultWebLoaders()
    {
        if (function_exists('curl_init')) {
            $this->loaders['https'] = new CurlWebLoader('https://', null, $this->decoderManager);
            $this->loaders['http']  = new CurlWebLoader('http://', null, $this->decoderManager);
        } else {
            $this->loaders['https'] = new FileGetContentsWebLoader('https://', $this->decoderManager);
            $this->loaders['http']  = new FileGetContentsWebLoader('http://', $this->decoderManager);
        }
    }

    /**
     * @return DecoderManager
     */
    public function getDecoderManager()
    {
        return $this->decoderManager;
    }
}
