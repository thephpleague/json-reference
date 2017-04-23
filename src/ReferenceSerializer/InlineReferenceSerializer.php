<?php

namespace League\JsonReference\ReferenceSerializer;

use League\JsonReference\Reference;
use League\JsonReference\ReferenceSerializationException;
use League\JsonReference\ReferenceSerializerInterface;

/**
 * A reference serializer that attempts to inline the referenced schema.
 */
final class InlineReferenceSerializer implements ReferenceSerializerInterface
{
    public function serialize(Reference $reference)
    {
        $stack  = [];
        $schema = $stack[] = $reference->resolve();

        while ($schema instanceof Reference) {
            $resolved = $schema->resolve();
            if (in_array($resolved, $stack)) {
                throw ReferenceSerializationException::circular($schema->getRef());
            }
            $schema = $resolved;
        }

        return $schema;
    }
}
