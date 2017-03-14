<?php

namespace League\JsonReference\ScopeResolvers;

use League\JsonReference\Pointer;
use function League\JsonReference\pointer_push;
use function League\JsonReference\resolve_uri;
use League\JsonReference\ScopeResolver;

final class JsonSchemaScopeResolver implements ScopeResolver
{
    const KEYWORD_DRAFT_4 = 'id';
    const KEYWORD_DRAFT_6 = '$id';

    /**
     * @var string
     */
    private $keyword;

    public function __construct($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($schema, $currentPointer, $currentScope)
    {
        $pointer     = new Pointer($schema);
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
