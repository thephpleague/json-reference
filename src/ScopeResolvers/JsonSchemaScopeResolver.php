<?php

namespace League\JsonReference\ScopeResolvers;

use function League\JsonReference\pointer;
use function League\JsonReference\pointer_push;
use function League\JsonReference\resolve_uri;
use League\JsonReference\ScopeResolverInterface;

final class JsonSchemaScopeResolver implements ScopeResolverInterface
{
    const KEYWORD_DRAFT_4 = 'id';
    const KEYWORD_DRAFT_6 = '$id';

    /**
     * @var string
     */
    private $keyword;

    /**
     * @param string $keyword
     */
    public function __construct($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($schema, $currentPointer, $currentScope)
    {
        $pointer     = pointer($schema);
        $currentPath = '';

        foreach (explode('/', $currentPointer) as $segment) {
            if (!empty($segment)) {
                $currentPath = pointer_push($currentPath, $segment);
            }
            if ($pointer->has($currentPath . '/' . $this->keyword)) {
                $id = $pointer->get($currentPath . '/' . $this->keyword);
                if (is_string($id)) {
                    $currentScope = resolve_uri($id, $currentScope);
                }
            }
        }

        return $currentScope;
    }
}
