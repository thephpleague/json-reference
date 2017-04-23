<?php

namespace League\JsonReference\ReferenceSerializer;

use League\JsonReference\Reference;
use League\JsonReference\ReferenceSerializerInterface;

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
