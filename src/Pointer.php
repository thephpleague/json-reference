<?php

namespace League\JsonReference;

use League\JsonReference\Pointer\InvalidPointerException;
use League\JsonReference\Pointer\NonExistentValue;

/**
 * A simple JSON Pointer implementation that can traverse
 * an object resulting from a json_decode() call.
 *
 * @see https://tools.ietf.org/html/rfc6901
 */
final class Pointer
{
    /**
     * @var object|array
     */
    private $json;

    /**
     * @param object|array $json
     */
    public function __construct(&$json)
    {
        $this->json = &$json;
    }

    /**
     * @param string $pointer
     *
     * @return mixed
     *
     * @throws InvalidPointerException
     */
    public function get($pointer)
    {
        $value = $this->traverse($pointer);

        if ($value instanceof NonExistentValue) {
            throw InvalidPointerException::nonexistentValue($value->getValue());
        }

        return $value;
    }

    /**
     * @param string $pointer
     *
     * @return bool
     */
    public function has($pointer)
    {
        return !$this->traverse($pointer) instanceof NonExistentValue;
    }

    /**
     * @param string $pointer
     * @param mixed  $data
     *
     * @return void
     *
     * @throws InvalidPointerException
     * @throws \InvalidArgumentException
     *
     */
    public function set($pointer, $data)
    {
        if ($pointer === '') {
            throw new \InvalidArgumentException('Cannot replace the object with set.');
        }

        $pointer = $this->parse($pointer);
        $replace = array_pop($pointer);
        $target  = &$this->getTarget($pointer);

        if (is_array($target)) {
            if ($replace === '-') {
                $target[] = $data;
            } else {
                $target[$replace] = $data;
            }
        } elseif (is_object($target)) {
            $target->$replace = $data;
        } else {
            throw InvalidPointerException::invalidTarget($target);
        }
    }

    /**
     * @param string $pointer
     *
     * @return void
     */
    public function remove($pointer)
    {
        if ($pointer === '') {
            throw new \InvalidArgumentException('Cannot remove the object.');
        }

        $pointer = $this->parse($pointer);
        $remove  = array_pop($pointer);
        $target  = &$this->getTarget($pointer);

        if (is_array($target)) {
            unset($target[$remove]);
        } elseif (is_object($target)) {
            unset($target->$remove);
        } else {
            throw InvalidPointerException::invalidTarget($target);
        }
    }

    /**
     * @param array $pointer
     *
     * @return mixed
     */
    private function &getTarget(array $pointer)
    {
        $target = &$this->json;

        foreach ($pointer as $segment) {
            if (is_array($target)) {
                $target =& $target[$segment];
            } else {
                $target =& $target->$segment;
            }
        }

        return $target;
    }

    /**
     * Returns the value referenced by the pointer or a NonExistentValue if the value does not exist.
     *
     * @param string $pointer The pointer
     *
     * @return mixed
     */
    private function traverse($pointer)
    {
        $pointer = $this->parse($pointer);
        $json    = $this->json;

        foreach ($pointer as $segment) {
            if ($json instanceof Reference) {
                if (!$json->has($segment)) {
                    return new NonExistentValue($segment);
                }
                $json = $json->get($segment);
            } elseif (is_object($json)) {
                // who does this?
                if ($segment === '' && property_exists($json, '_empty_')) {
                    $segment = '_empty_';
                }
                if (!property_exists($json, $segment)) {
                    return new NonExistentValue($segment);
                }
                $json = $json->$segment;
            } elseif (is_array($json)) {
                if (!array_key_exists($segment, $json)) {
                    return new NonExistentValue($segment);
                }
                $json = $json[$segment];
            } else {
                return new NonExistentValue($segment);
            }
        }

        return $json;
    }

    /**
     * Parses a JSON Pointer as defined in the specification.
     * @see https://tools.ietf.org/html/rfc6901#section-4
     *
     * @param string $pointer
     *
     * @return array
     *
     * @throws InvalidPointerException
     */
    private function parse($pointer)
    {
        if (!is_string($pointer)) {
            throw InvalidPointerException::invalidType(gettype($pointer));
        }

        if (!isset($pointer[0])) {
            return [];
        }

        // If the pointer is a url fragment, it needs to be url decoded.
        if ($pointer[0] === '#') {
            $pointer = urldecode(substr($pointer, 1));
        }

        // For convenience add the beginning slash if it's missing.
        if (isset($pointer[0]) && $pointer[0] !== '/') {
            $pointer = '/' . $pointer;
        }

        $pointer = array_slice(explode('/', $pointer), 1);
        return str_replace(['~1', '~0'], ['/', '~'], $pointer);
    }
}
