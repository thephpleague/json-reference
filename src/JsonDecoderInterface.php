<?php

namespace ActiveRules\JsonReference;

interface JsonDecoderInterface
{
    /**
     * @param string $json
     *
     * @return object
     *
     * @throws JsonDecodingException
     */
    public function decode($json);
}
