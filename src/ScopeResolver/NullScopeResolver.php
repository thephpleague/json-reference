<?php

namespace Activerules\JsonReference\ScopeResolver;

use Activerules\JsonReference\ScopeResolverInterface;

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
