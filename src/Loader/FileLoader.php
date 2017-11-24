<?php

namespace League\JsonReference\Loader;

use League\JsonReference\Decoder\JsonDecoder;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;

final class FileLoader implements LoaderInterface
{
    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @param DecoderInterface $jsonDecoder
     */
    public function __construct(DecoderInterface $jsonDecoder = null)
    {
        $this->jsonDecoder = $jsonDecoder ?: new JsonDecoder();
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
