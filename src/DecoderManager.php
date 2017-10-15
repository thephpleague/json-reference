<?php

namespace League\JsonReference;

use League\JsonReference\Decoder\JsonDecoder;
use League\JsonReference\Decoder\YamlDecoder;

final class DecoderManager
{

    
    /**
     * @var ParserInterface[]
     */
    private $decoders = [];

    /**
     * @var bool
     */
    private $ignoreUnknownExtension = [];

    /**
     * @param DecoderInterface[] $decoders
     */
    public function __construct(array $decoders = [], $ignoreUnknownExtension = true)
    {
        if (empty($decoders)) {
            $this->registerDefaultDecoder();
        }
        
        foreach ($decoders as $extension => $decoder) {
            $this->registerDecoder($extension, $decoder);
        }

        $this->ignoreUnknownExtension = $ignoreUnknownExtension;
    }

    /**
     * Register a DecoderInterface for the given extension.
     *
     * @param DecoderInterface $decoder
     */
    public function registerDecoder($extension, DecoderInterface $decoder)
    {
        $this->decoders[$extension] = $decoder;
    }

    /**
     * Get all registered decoders, keyed by the extensions they are registered to decode schemas for.
     *
     * @return DecoderInterface[]
     */
    public function getDecoders()
    {
        return $this->decoders;
    }

    /**
     * Set to true to use default decoder for unknown file extensions
     *
     * @param bool
     */
    public function setIgnoreUnknownExtension($ignoreUnknownExtension)
    {
        $this->ignoreUnknownExtension = $ignoreUnknownExtension;
    }

    /**
     * Get the decoder for the given extension.
     *
     * @param string $extension
     *
     * @return DecoderInterface
     * @throws \InvalidArgumentException
     */
    public function getDecoder($extension)
    {
        if (!$this->hasDecoder($extension)) {
            if ($this->ignoreUnknownExtension) {
                $extension = 'json';
            } else {
                throw new \InvalidArgumentException(
                    sprintf('A decoder is not registered for the extension "%s"', $extension)
                );
            }
        }

        return $this->decoders[$extension];
    }
    
    /**
     * @param string $extension
     *
     * @return bool
     */
    public function hasDecoder($extension)
    {
        return isset($this->decoders[$extension]);
    }

    /**
     * Register the default decoder.
     */
    private function registerDefaultDecoder()
    {
        $this->registerDecoder('json', new JsonDecoder());
        $this->registerDecoder('yaml', new YamlDecoder());
        $this->registerDecoder('yml', $this->decoders['yaml']);
    }
}
