<?php

namespace Activerules\JsonReference\ReferenceSerializer;

use Activerules\JsonReference\Reference;
use Activerules\JsonReference\ReferenceSerializerInterface;

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
