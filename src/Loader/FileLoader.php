<?php

namespace League\JsonReference\Loader;

use League\JsonReference\JsonDecoder\JsonDecoder;
use League\JsonReference\JsonDecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;

final class FileLoader implements LoaderInterface
{
    /**
     * @var JsonDecoderInterface
     */
    private $jsonDecoder;

    /**
     * @param JsonDecoderInterface $jsonDecoder
     */
    public function __construct(JsonDecoderInterface $jsonDecoder = null)
    {
        $this->jsonDecoder = $jsonDecoder ?: new JsonDecoder();
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        // file:// + path without query
        $path = 'file://'.explode('?', $path, 2)[0];

        if (!file_exists($path)) {
            throw SchemaLoadingException::notFound($path);
        }

        return $this->jsonDecoder->decode(file_get_contents($path));
    }
}
