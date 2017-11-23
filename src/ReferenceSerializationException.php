<?php

namespace ActiveRules\JsonReference;

final class ReferenceSerializationException extends \RuntimeException
{
    public static function circular($ref)
    {
        return new static(sprintf('A circular reference was encountered while serializing %s', $ref));
    }
}
