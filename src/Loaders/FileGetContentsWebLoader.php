<?php

namespace League\JsonReference\Loaders;

use League\JsonReference\JsonDecoder;
use League\JsonReference\JsonDecoders\StandardJsonDecoder;
use League\JsonReference\Loader;
use League\JsonReference\SchemaLoadingException;

final class FileGetContentsWebLoader implements Loader
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var JsonDecoder
     */
    private $jsonDecoder;

    /**
     * @param string      $prefix
     * @param JsonDecoder $jsonDecoder
     */
    public function __construct($prefix, JsonDecoder $jsonDecoder = null)
    {
        $this->prefix      = $prefix;
        $this->jsonDecoder = $jsonDecoder ?: new StandardJsonDecoder();
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $uri = $this->prefix . $path;
        set_error_handler(function () use ($uri) {
            throw SchemaLoadingException::create($uri);
        });
        $response = file_get_contents($uri);
        restore_error_handler();

        if (!$response) {
            throw SchemaLoadingException::create($uri);
        }

        return $this->jsonDecoder->decode($response);
    }
}
