<?php

namespace League\JsonReference\Loaders;

use League\JsonReference\JsonDecoder;
use League\JsonReference\JsonDecoders\StandardJsonDecoder;
use League\JsonReference\Loader;
use League\JsonReference\SchemaLoadingException;

final class FileLoader implements Loader
{
    /**
     * @var JsonDecoder
     */
    private $jsonDecoder;

    /**
     * @param JsonDecoder $jsonDecoder
     */
    public function __construct(JsonDecoder $jsonDecoder = null)
    {
        $this->jsonDecoder = $jsonDecoder ?: new StandardJsonDecoder();
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        if (!file_exists($path)) {
            throw SchemaLoadingException::notFound($path);
        }

        return $this->jsonDecoder->decode(file_get_contents($path));
    }
}
