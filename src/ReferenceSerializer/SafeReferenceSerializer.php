<?php

namespace ActiveRules\JsonReference\ReferenceSerializer;

use ActiveRules\JsonReference\Reference;
use ActiveRules\JsonReference\ReferenceSerializerInterface;

/**
 * A reference serializer that returns the original reference.
 */
final class SafeReferenceSerializer implements ReferenceSerializerInterface
{
    public function serialize(Reference $reference)
    {
        return ['$ref' => $reference->getRef()];
    }
}
