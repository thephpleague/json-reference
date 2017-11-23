<?php

namespace ActiveRules\JsonReference\Loader;

use ActiveRules\JsonReference\JsonDecoder\JsonDecoder;
use ActiveRules\JsonReference\JsonDecoderInterface;
use ActiveRules\JsonReference\LoaderInterface;
use ActiveRules\JsonReference\SchemaLoadingException;

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
        // Check if the file path is 'relative', i.e. starts with a dot.
        if(substr($path,0,1) === '.') {
            // If the path is 'relative' check for an defined AR_JSON_SCHEMA_DIR
            $schemaDir = getenv('AR_JSON_SCHEMA_DIR');

            // If root dir isn't set use the default relative path of './schema'
            if(! $schemaDir) {
                $schemaDir = getcwd() . '/schema';
            }
          
            // Define the full path to pass to existing League Reference File Loader
            $path = $schemaDir . ltrim($path, '.');
        }
        
        if (!file_exists($path)) {
            throw SchemaLoadingException::notFound($path);
        }

        return $this->jsonDecoder->decode(file_get_contents($path));
    }
}
