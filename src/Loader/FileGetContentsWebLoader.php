<?php

namespace League\JsonReference\Loader;

use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;

final class FileGetContentsWebLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var JsonDecoderInterface
     */
    private $jsonDecoder;

    /**
     * @param string               $prefix
     * @param JsonDecoderInterface $jsonDecoder
     */
    public function __construct($prefix, DecoderManager $decoders = null)
    {
        $this->prefix      = $prefix;
        $this->decoders = $decoders ?: new DecoderManager();
    }

    /**
     * {@inheritdoc}
     */
    public function load($path, $defaultExtension = 'json')
    {
        $uri       = $this->prefix . $path;
        $extension = isset(pathinfo($path)['extension']) ? pathinfo($path)['extension'] : $defaultExtension;

        set_error_handler(
            function () use ($uri) {
                throw SchemaLoadingException::create($uri);
            }
        );
        $response = file_get_contents($uri);
        restore_error_handler();

        if (!$response) {
            throw SchemaLoadingException::create($uri);
        }

        return $this->decoders->getDecoder($extension)->decode($response);
    }
}
