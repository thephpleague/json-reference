<?php

namespace League\JsonReference\Loaders;

use League\JsonReference\JsonDecoderInterface;
use League\JsonReference\JsonDecoders\JsonDecoder;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;

final class ArrayLoader implements LoaderInterface
{
    /**
     * @var array
     */
    private $schemas;

    /**
     * @var JsonDecoderInterface
     */
    private $jsonDecoder;

    /**
     * @param array                $schemas      A map of schemas where path => schema.The schema should be a string or the
     *                                  object resulting from a json_decode call.
     * @param JsonDecoderInterface $jsonDecoder
     */
    public function __construct(array $schemas, JsonDecoderInterface $jsonDecoder = null)
    {
        $this->schemas     = $schemas;
        $this->jsonDecoder = $jsonDecoder ?: new JsonDecoder();
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
