<?php

namespace Activerules\JsonReference\JsonDecoder;

use Activerules\JsonReference\JsonDecoderInterface;
use Activerules\JsonReference\JsonDecodingException;

final class JsonDecoder implements JsonDecoderInterface
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
     * @param string $json
     *
     * @return object
     */
    public function decode($json)
    {
        $data = json_decode($json, $this->assoc, $this->depth, $this->options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodingException(sprintf('Invalid JSON: %s', json_last_error_msg()));
        }

        return $data;
    }
}
