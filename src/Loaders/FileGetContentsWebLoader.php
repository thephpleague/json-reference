<?php

namespace League\JsonReference\Loaders;

use League\JsonReference;
use League\JsonReference\Loader;
use League\JsonReference\SchemaLoadingException;

final class FileGetContentsWebLoader implements Loader
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
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

        return JsonReference\json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
    }
}
