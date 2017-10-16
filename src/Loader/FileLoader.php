<?php

namespace League\JsonReference\Loader;

use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;

final class FileLoader implements LoaderInterface
{
    /**
     * @var DecoderManager
     */
    private $decoders;

    /**
     * @param JsonDecoderInterface|DecoderManager $decoders
     */
    public function __construct($decoders = null)
    {
        if ($decoders instanceof DecoderInterface) {
            $this->decoders = new DecoderManager([$decoders]);
        } else {
            $this->decoders = $decoders ?: new DecoderManager();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($path, $defaultExtension = 'json')
    {
        $path      = 'file://'.$path;
        $extension = isset(pathinfo($path)['extension']) ? pathinfo($path)['extension'] : $defaultExtension;

        if (!file_exists($path)) {
            throw SchemaLoadingException::notFound($path);
        }

        return $this->decoders->getDecoder($extension)->decode(file_get_contents($path));
    }
}
