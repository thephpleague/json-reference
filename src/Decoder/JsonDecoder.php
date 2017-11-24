<?php

namespace League\JsonReference\Decoder;

use League\JsonReference\DecoderInterface;
use League\JsonReference\DecodingException;

class JsonDecoder implements DecoderInterface
{
    /**
     * @var bool
     */
    private $assoc;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $options;

    /**
     * @param bool $assoc
     * @param int  $depth
     * @param int  $options
     */
    public function __construct($assoc = false, $depth = 512, $options = JSON_BIGINT_AS_STRING)
    {
        $this->assoc   = $assoc;
        $this->depth   = $depth;
        $this->options = $options;
    }

    
    /**
     * {@inheritdoc}
     */
    public function decode($schema)
    {
        $data = json_decode($schema, $this->assoc, $this->depth, $this->options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DecodingException(sprintf('Invalid JSON: %s', json_last_error_msg()));
        }

        return $data;
    }
}
