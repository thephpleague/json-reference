<?php

namespace League\JsonReference\Loader;

use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;
use function League\JsonReference\determineMediaType;

final class FileLoader implements LoaderInterface
{
    /**
     * @var DecoderManager
     */
    private $decoderManager;

    /**
     * @param DecoderInterface|DecoderManager $decoderManager
     */
    public function __construct($decoderManager = null)
    {
        if ($decoderManager instanceof DecoderInterface) {
            $this->decoderManager = new DecoderManager([null => $decoderManager]);
        } else {
            $this->decoderManager = $decoderManager ?: new DecoderManager();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $uri = 'file://' . $path;
        
        if (!file_exists($uri)) {
            throw SchemaLoadingException::notFound($uri);
        }

        $type = determineMediaType(['uri' => $uri]);
        return $this->decoderManager->getDecoder($type)->decode(file_get_contents($uri));
    }
}
