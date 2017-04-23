<?php

namespace League\JsonReference;

/**
 * An interface for the dereferencer to allow decorating.
 */
interface DereferencerInterface
{
    /**
     * Return the schema with all references resolved.
     *
     * @param string|object $schema Either a valid path like "http://json-schema.org/draft-03/schema#"
     *                              or the object resulting from a json_decode call.
     *
     * @param string $uri
     *
     * @return object
     */
    public function dereference($schema, $uri = '');

    /**
     * @return LoaderManager
     */
    public function getLoaderManager();

    /**
     * @return \League\JsonReference\ScopeResolverInterface
     */
    public function getScopeResolver();

    /**
     * @return \League\JsonReference\ReferenceSerializerInterface
     */
    public function getReferenceSerializer();
}
