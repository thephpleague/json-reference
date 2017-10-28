<?php

namespace League\JsonReference;

interface DecoderInterface
{
    /**
     * @param string $schema
     *
     * @return object
     *
     * @throws DecodingException
     */
    public function decode($schema);
}
