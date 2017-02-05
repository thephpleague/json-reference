<?php

namespace League\JsonReference;

interface ScopeResolver
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
