<?php

namespace League\JsonReference\Decoder;

use League\JsonReference\DecoderInterface;
use League\JsonReference\DecodingException;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class YamlDecoder implements DecoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function decode($schema)
    {
        try {
            return Yaml::parse($schema, Yaml::PARSE_OBJECT_FOR_MAP | Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
        } catch (ParseException $e) {
            throw new DecodingException(sprintf('Invalid Yaml: %s', $e->getMessage()), $e->getCode(), $e);
        }
    }
}
