<?php

namespace ActiveRules\JsonReference\ScopeResolver;

use ActiveRules\JsonReference\ScopeResolverInterface;

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
