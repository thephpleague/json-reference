<?php

namespace Activerules\JsonReference;

interface ReferenceSerializerInterface
{
    /**
     * @param \Activerules\JsonReference\Reference $reference
     *
     * @return mixed data which can be serialized by json_encode,
     *                    which is a value of any type other than a resource.
     */
    public function serialize(Reference $reference);
}
