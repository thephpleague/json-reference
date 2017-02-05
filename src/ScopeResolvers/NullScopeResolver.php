<?php

namespace League\JsonReference\ScopeResolvers;

use League\JsonReference\ScopeResolver;

final class NullScopeResolver implements ScopeResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve($schema, $currentPointer, $currentScope)
    {
        return $currentScope;
    }
}
