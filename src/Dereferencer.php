<?php

namespace Activerules\JsonReference;

use Activerules\JsonReference\ReferenceSerializer\SafeReferenceSerializer;
use Activerules\JsonReference\ScopeResolver\JsonSchemaScopeResolver;
use Activerules\JsonReference\ScopeResolver\NullScopeResolver;

final class Dereferencer implements DereferencerInterface
{
    /**
     * @var LoaderManager
     */
    private $loaderManager;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var ReferenceSerializerInterface
     */
    private $referenceSerializer;

    /**
     * Create a new Dereferencer.
     *
     * @param ScopeResolverInterface                             $scopeResolver
     * @param \Activerules\JsonReference\ReferenceSerializerInterface $referenceSerializer
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver = null,
        ReferenceSerializerInterface $referenceSerializer = null
    ) {
        $this->scopeResolver       = $scopeResolver ?: new NullScopeResolver();
        $this->referenceSerializer = $referenceSerializer ?: new SafeReferenceSerializer();
        $this->loaderManager       = new LoaderManager();

        Reference::setDereferencerInstance($this);
    }

    /**
     * Create a new dereferencer configured for dereferencing JSON Schema Draft4 schemas.
     *
     * @return \Activerules\JsonReference\Dereferencer
     */
    public static function draft4()
    {
        return new self(new JsonSchemaScopeResolver(JsonSchemaScopeResolver::KEYWORD_DRAFT_4));
    }

    /**
     * Create a new dereferencer configured for dereferencing JSON Schema Draft6 schemas.
     *
     * @return \Activerules\JsonReference\Dereferencer
     */
    public static function draft6()
    {
        return new self(new JsonSchemaScopeResolver(JsonSchemaScopeResolver::KEYWORD_DRAFT_6));
    }

    /**
     * {@inheritdoc}
     */
    public function dereference($schema, $uri = '')
    {
        return $this->crawl($schema, $uri, function ($schema, $pointer, $ref, $scope) {
            $resolved = new Reference($ref, $scope, is_internal_ref($ref) ? $schema : null);
            return merge_ref($schema, $resolved, $pointer);
        });
    }

    /**
     * @param object|string $schema
     * @param string        $uri
     *
     * @return array
     */
    private function prepareArguments($schema, $uri)
    {
        if (is_string($schema)) {
            $uri    = $schema;
            $schema = resolve_fragment($uri, $this->loadExternalRef($uri));
            $uri    = strip_fragment($uri);
        }

        return [$schema, $uri];
    }

    /**
     * @return LoaderManager
     */
    public function getLoaderManager()
    {
        return $this->loaderManager;
    }

    /**
     * @param \Activerules\JsonReference\LoaderManager $loaderManager
     *
     * @return \Activerules\JsonReference\Dereferencer
     */
    public function setLoaderManager(LoaderManager $loaderManager)
    {
        $this->loaderManager = $loaderManager;

        return $this;
    }

    /**
     * @return \Activerules\JsonReference\ScopeResolverInterface
     */
    public function getScopeResolver()
    {
        return $this->scopeResolver;
    }

    /**
     * @param \Activerules\JsonReference\ScopeResolverInterface $scopeResolver
     *
     * @return \Activerules\JsonReference\Dereferencer
     */
    public function setScopeResolver(ScopeResolverInterface $scopeResolver)
    {
        $this->scopeResolver = $scopeResolver;

        return $this;
    }

    /**
     * @return \Activerules\JsonReference\ReferenceSerializerInterface
     */
    public function getReferenceSerializer()
    {
        return $this->referenceSerializer;
    }

    /**
     * @param \Activerules\JsonReference\ReferenceSerializerInterface $referenceSerializer
     *
     * @return \Activerules\JsonReference\Dereferencer
     */
    public function setReferenceSerializer(ReferenceSerializerInterface $referenceSerializer)
    {
        $this->referenceSerializer = $referenceSerializer;

        return $this;
    }

    /**
     * @param string|object $schema
     * @param string        $uri
     * @param callable      $resolver
     *
     * @return object
     */
    private function crawl($schema, $uri, callable $resolver)
    {
        list($schema, $uri) = $this->prepareArguments($schema, $uri);

        foreach (schema_extract($schema, 'Activerules\JsonReference\is_ref') as $pointer => $ref) {
            $scope = $this->scopeResolver->resolve($schema, $pointer, $uri);

            $schema = $resolver($schema, $pointer, $ref, $scope);
        }

        return $schema;
    }

    /**
     * Load an external ref and return the JSON object.
     *
     * @param string $reference
     *
     * @return object
     */
    private function loadExternalRef($reference)
    {
        list($prefix, $path) = parse_external_ref($reference);
        return $this->loaderManager->getLoader($prefix)->load($path);
    }
}
