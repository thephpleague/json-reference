<?php

namespace League\JsonReference;

use Sabre\Uri;

/**
 * @param string $json
 * @param bool   $assoc
 * @param int    $depth
 * @param int    $options
 * @return mixed
 * @throws \InvalidArgumentException
 */
function json_decode($json, $assoc = false, $depth = 512, $options = 0)
{
    $data = \json_decode($json, $assoc, $depth, $options);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \InvalidArgumentException(sprintf('Invalid JSON: %s', json_last_error_msg()));
    }

    return $data;
}

/**
 * Escape a JSON Pointer.
 *
 * @param  string $pointer
 * @return string
 */
function escape_pointer($pointer)
{
    $pointer = str_replace('~', '~0', $pointer);
    return str_replace('/', '~1', $pointer);
}


/**
 * Push a segment onto the given JSON Pointer.
 *
 * @param string   $pointer
 * @param string[] $segments
 *
 * @return string
 *
 */
function pointer_push($pointer, ...$segments)
{
    $segments =  array_map('League\JsonReference\escape_pointer', $segments);
    return $pointer . '/' . implode('/', $segments);
}

/**
 * Removes the fragment from a reference.
 *
 * @param  string $ref
 * @return string
 */
function strip_fragment($ref)
{
    $fragment = parse_url($ref, PHP_URL_FRAGMENT);

    return $fragment ? str_replace($fragment, '', $ref) : $ref;
}

/**
 * Check if the reference contains a fragment and resolve
 * the pointer.  Otherwise returns the original schema.
 *
 * @param  string $ref
 * @param  object $schema
 *
 * @return object
 */
function resolve_fragment($ref, $schema)
{
    $fragment = parse_url($ref, PHP_URL_FRAGMENT);

    if (!is_internal_ref($ref) && is_string($fragment)) {
        return (new Pointer($schema))->get($fragment);
    }

    return $schema;
}

/**
 * @param string $keyword
 * @param mixed  $value
 *
 * @return bool
 */
function is_ref($keyword, $value)
{
    return $keyword === '$ref' && is_string($value);
}

/**
 * Determine if a reference is relative.
 * A reference is relative if it does not being with a prefix.
 *
 * @param string $ref
 *
 * @return bool
 */
function is_relative_ref($ref)
{
    return !preg_match('#^.+\:\/\/.*#', $ref);
}

/**
 * @param string $value
 *
 * @return bool
 */
function is_internal_ref($value)
{
    return is_string($value) && substr($value, 0, 1) === '#';
}

/**
 * Parse an external reference returning the prefix and path.
 *
 * @param string $ref
 *
 * @return array
 *
 * @throws \InvalidArgumentException
 */
function parse_external_ref($ref)
{
    if (is_relative_ref($ref)) {
        throw new \InvalidArgumentException(
            sprintf(
                'The path  "%s" was expected to be an external reference but is missing a prefix.  ' .
                'The schema path should start with a prefix i.e. "file://".',
                $ref
            )
        );
    }

    list($prefix, $path) = explode('://', $ref, 2);
    $path = rtrim(strip_fragment($path), '#');

    return [$prefix, $path];
}

/**
 * Resolve the given id against the parent scope and return the resolved URI.
 *
 * @param string $id          The id to resolve.  This should be a valid relative or absolute URI.
 * @param string $parentScope The parent scope to resolve against.  Should be a valid URI or empty.
 *
 * @return string
 */
function resolve_uri($id, $parentScope)
{
    // If there is no parent scope, there is nothing to resolve against.
    if ($parentScope === '') {
        return $id;
    }

    return Uri\resolve($parentScope, $id);
}

/**
 * Recursively iterates over each value in the schema passing them to the callback function.
 * If the callback function returns true the value is returned into the result array, keyed by a JSON Pointer.
 *
 * @param mixed    $schema
 * @param callable $callback
 * @param string   $pointer
 *
 * @return array
 */
function schema_extract($schema, callable $callback, $pointer = '')
{
    $matches = [];

    if ((!is_array($schema) && !is_object($schema)) || $schema instanceof Reference) {
        return $matches;
    }

    foreach ($schema as $keyword => $value) {
        switch (true) {
            case is_object($value):
                $matches = array_merge($matches, schema_extract($value, $callback, pointer_push($pointer, $keyword)));
                break;
            case is_array($value):
                foreach ($value as $k => $v) {
                    $matches = array_merge(
                        $matches,
                        schema_extract($v, $callback, pointer_push($pointer, $keyword, $k))
                    );
                }
                break;
            case $callback($keyword, $value):
                $matches[$pointer] = $value;
                break;
        }
    }

    return $matches;
}

/**
 * @param object $schema
 * @param object $resolvedRef
 * @param string $path
 *
 * @return void
 */
function merge_ref($schema, $resolvedRef, $path = '')
{
    if ($path === '') {
        // Immediately resolve root references, because
        // get_object_vars does not work for reference proxies.
        while ($resolvedRef instanceof Reference) {
            $resolvedRef = $resolvedRef->resolve();
        }
        unset($schema->{'$ref'});
        foreach (get_object_vars($resolvedRef) as $prop => $value) {
            $schema->$prop = $value;
        }
        return;
    }

    $pointer = new Pointer($schema);
    if ($pointer->has($path)) {
        $pointer->set($path, $resolvedRef);
    }
}
