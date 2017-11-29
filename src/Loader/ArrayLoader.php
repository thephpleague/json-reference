<?php

namespace League\JsonReference\Loader;

use League\JsonReference\DecoderManager;
use League\JsonReference\DecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;
use function League\JsonReference\determineMediaType;

final class ArrayLoader implements LoaderInterface
{
    /**
     * @var array
     */
    private $schemas;

    /**
     * @var DecoderManager
     */
    private $decoderManager;

    /**
     * @param array $schemas  A map of schemas where path => schema.The schema should be a string or the
     *                        object resulting from a json_decode call.
     *
     * @param DecoderInterface|DecoderManager $decoderManager
     */
    public function __construct(array $schemas, $decoderManager = null)
    {
        $this->schemas = $schemas;
        
        if ($decoderManager instanceof DecoderInterface) {
            $this->decoderManager = new DecoderManager([null => $decoderManager]);
        } else {
            $this->decoderManager = $decoderManager ?: new DecoderManager();
        }
    }

    /**
     * @param string $schema
     * @param string $type
     *
     * @return object
     *
     * @throws DecodingException
     */
    public function load($path)
    {
        if (!array_key_exists($path, $this->schemas)) {
            throw SchemaLoadingException::notFound($path);
        }

        $schema = $this->schemas[$path];

        if (is_string($schema)) {
            $type = determineMediaType(['uri' => $path]);
            return $this->decoderManager->getDecoder($type)->decode($schema);
        } elseif (is_object($schema)) {
            return $schema;
        } else {
            throw SchemaLoadingException::create($path);
        }
    }
}
