<?php

namespace League\JsonReference\Loaders;

use League\JsonReference\JsonDecoder;
use League\JsonReference\JsonDecoders\StandardJsonDecoder;
use League\JsonReference\Loader;
use League\JsonReference\SchemaLoadingException;

final class ArrayLoader implements Loader
{
    /**
     * @var array
     */
    private $schemas;

    /**
     * @var JsonDecoder
     */
    private $jsonDecoder;

    /**
     * @param array       $schemas      A map of schemas where path => schema.The schema should be a string or the
     *                                  object resulting from a json_decode call.
     * @param JsonDecoder $jsonDecoder
     */
    public function __construct(array $schemas, JsonDecoder $jsonDecoder = null)
    {
        $this->schemas     = $schemas;
        $this->jsonDecoder = $jsonDecoder ?: new StandardJsonDecoder();
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        if (!array_key_exists($path, $this->schemas)) {
            throw SchemaLoadingException::notFound($path);
        }

        $schema = $this->schemas[$path];

        if (is_string($schema)) {
            return $this->jsonDecoder->decode($schema);
        } elseif (is_object($schema)) {
            return $schema;
        } else {
            throw SchemaLoadingException::create($path);
        }
    }
}
