<?php

namespace League\JsonReference\Loaders;

use League\JsonReference\Loader;
use League\JsonReference\SchemaLoadingException;

/**
 * This loader takes two other loaders as constructor parameters, and will
 * attempt to load from the first loader before deferring to the second loader.
 * This is useful when you would like to use multiple loaders for the same prefix.
 */
final class ChainableLoader implements Loader
{
    /**
     * @var Loader
     */
    private $firstLoader;

    /**
     * @var Loader
     */
    private $secondLoader;

    /**
     * @param \League\JsonReference\Loader $firstLoader
     * @param \League\JsonReference\Loader $secondLoader
     */
    public function __construct(Loader $firstLoader, Loader $secondLoader)
    {
        $this->firstLoader  = $firstLoader;
        $this->secondLoader = $secondLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        try {
            return $this->firstLoader->load($path);
        } catch (SchemaLoadingException $e) {
            return $this->secondLoader->load($path);
        }
    }
}
