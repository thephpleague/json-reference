<?php

namespace ActiveRules\JsonReference\Loader;

use ActiveRules\JsonReference\LoaderInterface;
use ActiveRules\JsonReference\SchemaLoadingException;

/**
 * This loader takes two other loaders as constructor parameters, and will
 * attempt to load from the first loader before deferring to the second loader.
 * This is useful when you would like to use multiple loaders for the same prefix.
 */
final class ChainedLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $firstLoader;

    /**
     * @var LoaderInterface
     */
    private $secondLoader;

    /**
     * @param \ActiveRules\JsonReference\LoaderInterface $firstLoader
     * @param \ActiveRules\JsonReference\LoaderInterface $secondLoader
     */
    public function __construct(LoaderInterface $firstLoader, LoaderInterface $secondLoader)
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
