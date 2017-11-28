<?php

namespace League\JsonReference;

use Sabre\Uri;

/**
 * @param object|array $json
 *
 * @return Pointer
 */
function pointer(&$json)
{
    return new Pointer($json);
}

/**
 * Escape a JSON Pointer.
 *
 * @param  string $pointer
 * @return string
 */
function escape_pointer($pointer)
{
    return str_replace(['~', '/'], ['~0', '~1'], $pointer);
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
    $segments = str_replace(['~', '/'], ['~0', '~1'], $segments);
    return ($pointer !== '/' ? $pointer : '') . '/' . implode('/', $segments);
}

/**
 * Removes the fragment from a reference.
 *
 * @param  string $ref
 * @return string
 */
function strip_fragment($ref)
{
    $fragment = Uri\parse($ref)['fragment'];

    return $fragment ? str_replace('#'.$fragment, '#', $ref) : $ref;
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
    $fragment = Uri\parse($ref)['fragment'];

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

    if ($schema instanceof Reference || (!is_array($schema) && !is_object($schema))) {
        return $matches;
    }

    foreach ($schema as $keyword => $value) {
        switch (true) {
            case is_object($value):
                $matches = array_merge($matches, schema_extract($value, $callback, pointer_push($pointer, $keyword)));
                break;
            case is_array($value):
                foreach ($value as $k => $v) {
                    if ($callback($k, $v)) {
                        $matches[pointer_push($pointer, $keyword)] = $v;
                    } else {
                        $matches = array_merge(
                            $matches,
                            schema_extract($v, $callback, pointer_push($pointer, $keyword, $k))
                        );
                    }
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
 * @return object
 */
function merge_ref($schema, $resolvedRef, $path = '')
{
    if ($path === '') {
        pointer($schema)->remove('$ref');
        foreach ($resolvedRef as $prop => $value) {
            pointer($schema)->set($prop, $value);
        }
        return $schema;
    }

    $pointer = new Pointer($schema);
    if ($pointer->has($path)) {
        $pointer->set($path, $resolvedRef);
    }
    return $schema;
}

/**
 * Parses Content-Type header and returns an array with type, subtype, suffix and parameters
 *
 * @param string $contentType
 *
 * @return array
 */
function parseContentTypeHeader($contentType)
{
    preg_match('%
        ^
            (?<type>[^\/;+]+)
            (
                \/
                (?<subtype>[^;+]*)
                (?<has_suffix>\+(?<suffix>[^;]*))?
                ;?
                (?<parameter>.*)?
            )
        $
        %x', $contentType, $matches);

    $result = [
        'type' => strtolower($matches['type']),
        'subtype' => strtolower($matches['subtype']),
        'suffix' => $matches['has_suffix'] ? strtolower($matches['suffix']) : null,
        'parameter' => null
    ];

    if ($matches['parameter']) {
        $result['parameter'] = [];
        
        preg_match_all('/\s*([^=;]+)\s*(=([^;]*))?;?/', $matches['parameter'], $parameters, PREG_SET_ORDER);

        foreach ($parameters as $parameter) {
            $result['parameter'][strtolower($parameter[1])] = isset($parameter[3]) ? $parameter[3] : '';
        }
    }

    return $result;
}

/**
 * Determine the file type based on the given context (http-headers, uri)
 *
 * @param mixed $context
 *
 * @return string
 */
function determineMediaType($context)
{
    if (isset($context['headers']) && $context['headers']) {
        if (isset($context['headers']['Content-Type'])) {
            $context['Content-Type'] = $context['headers']['Content-Type'];
        }
    }

    $type = null;

    if (isset($context['Content-Type']) && $context['Content-Type']) {
        $contentType = parseContentTypeHeader($context['Content-Type']);

        if (isset($contentType['suffix'])) {
            return '+'.$contentType['suffix'];
        } else {
            $type = $contentType['type'].'/'.$contentType['subtype'];
        }
    }

    $extension = null;

    if (isset($context['uri']) && $context['uri']) {
        $path = Uri\parse($context['uri'])['path'];
        $info = pathinfo($path);

        if (isset($info['extension'])) {
            $extension = $info['extension'];
        }
    }

    if ($type === 'application/octet-stream' && $extension) {
        return $extension;
    } elseif ($type) {
        return $type;
    } elseif ($extension) {
        return $extension;
    } else {
        return null;
    }
}
