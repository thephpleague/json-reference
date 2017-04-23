<?php

namespace League\JsonReference\ScopeResolvers;

use League\JsonReference\ScopeResolverInterface;

final class NullScopeResolver implements ScopeResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($schema, $currentPointer, $currentScope)
    {
        return $currentScope;
    }
}
