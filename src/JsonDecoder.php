<?php

namespace League\JsonReference;

interface JsonDecoder
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
