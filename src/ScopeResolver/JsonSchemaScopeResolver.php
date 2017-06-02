<?php

namespace League\JsonReference\ScopeResolver;

use League\JsonReference\ScopeResolverInterface;
use function League\JsonReference\resolve_uri;

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
        $current = $schema;
        foreach (explode('/', $currentPointer) as $segment) {
            if (isset($current->$segment)) {
                $current = $current->$segment;
            }
            $id = isset($current->{$this->keyword}) ? $current->{$this->keyword} : null;
            if (is_string($id)) {
                $currentScope = resolve_uri($id, $currentScope);
            }
        }

        return $currentScope;
    }
}
