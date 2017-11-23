<?php

namespace Activerules\JsonReference;

interface ScopeResolverInterface
{
    /**
     * @param object $schema
     * @param string $currentPointer
     * @param string $currentScope
     *
     * @return string
     */
    public function resolve($schema, $currentPointer, $currentScope);
}
