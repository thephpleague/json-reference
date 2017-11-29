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
     * @param DecoderInterface[] $decoders
     * @param string $defaultType
     */
    public function __construct(array $decoders = [], $defaultType = null)
    {
        if (empty($decoders)) {
            $this->registerDefaultDecoders();
            
            // Backwards compatibilty
            if ($defaultType === null) {
                $defaultType = 'json';
            }
        } else {
            foreach ($decoders as $type => $decoder) {
                $this->registerDecoder($type, $decoder);
            }
        }

        if ($defaultType) {
            $this->setDefaultType($defaultType);
        }
    }

    /**
     * Register a DecoderInterface for the given MIME-types.
     *
     * @param string $type
     * @param DecoderInterface $decoder
     */
    public function registerDecoder($type, DecoderInterface $decoder = null)
    {
        if ($decoder) {
            $this->decoders[$type] = $decoder;
        } else {
            unset($this->decoders[$type]);
        }
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
     * Set the default type for unknown files
     *
     * @param string defaultType
     */
    public function setDefaultType($defaultType = null)
    {
        $this->registerDecoder(null, $defaultType ? $this->getDecoder($defaultType) : null);
    }

    /**
     * Get the decoder for the given extension.
     *
     * @param string $type
     *
     * @return DecoderInterface
     * @throws \InvalidArgumentException
     */
    public function getDecoder($type)
    {
        if (!$this->hasDecoder($type)) {
            if ($this->hasDecoder(null)) {
                $type = null;
            } else {
                throw new \InvalidArgumentException(
                    sprintf('A decoder is not registered for the extension "%s"', $type)
                );
            }
        }

        return $this->decoders[$type];
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasDecoder($type)
    {
        return isset($this->decoders[$type]);
    }

    /**
     * Register the default decoders
     */
    private function registerDefaultDecoders()
    {
        $this->registerJsonDecoder();
    }

    /**
     * @param DecoderInterface $decoder
     */
    public function registerJsonDecoder(DecoderInterface $decoder = null)
    {
        $decoder = $decoder ?: new JsonDecoder();

        $this->registerDecoder('json', $decoder);
        $this->registerDecoder('text/json', $decoder);
        $this->registerDecoder('application/json', $decoder);
        $this->registerDecoder('+json', $decoder);
    }

    /**
     * @param DecoderInterface $decoder
     */
    public function registerYamlDecoder(DecoderInterface $decoder = null)
    {
        $decoder = $decoder ?: new YamlDecoder();

        $this->registerDecoder('yml', $decoder);
        $this->registerDecoder('yaml', $decoder);
        $this->registerDecoder('text/yaml', $decoder);
        $this->registerDecoder('application/x-yaml', $decoder);
        $this->registerDecoder('+yaml', $decoder);
    }
}
