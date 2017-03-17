<?php

namespace League\JsonReference;

use League\JsonReference\Pointer\InvalidPointerException;

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
    public function __construct($json)
    {
        $this->json = $json;
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
        $pointer = $this->parse($pointer);

        return $this->traverse($this->json, $pointer);
    }

    /**
     * @param string $pointer
     *
     * @return bool
     */
    public function has($pointer)
    {
        try {
            $this->get($pointer);

            return true;
        } catch (InvalidPointerException $e) {
            return false;
        }
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
            unset ($target[$remove]);
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
        $target = $this->json;

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
     * @param mixed $json    The result of a json_decode call or a portion of it.
     * @param array $pointer The parsed pointer
     *
     * @return mixed
     */
    private function traverse($json, $pointer)
    {
        // If we are out of pointers to process we are done.
        if (empty($pointer)) {
            return $json;
        }

        $reference = array_shift($pointer);

        if (!is_array($json) && !is_object($json)) {
            throw InvalidPointerException::nonexistentValue($reference);
        }

        if (is_array($json)) {
            if (!array_key_exists($reference, $json)) {
                throw InvalidPointerException::nonexistentValue($reference);
            }
            $json = $json[$reference];
        } else {
            // who does this?
            if ($reference === '' && property_exists($json, '_empty_')) {
                $reference = '_empty_';
            }
            if (!property_exists($json, $reference)) {
                throw InvalidPointerException::nonexistentValue($reference);
            }
            $json = $json->$reference;
        }

        return $this->traverse($json, $pointer);
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

        if ($pointer !== '' && strpos($pointer, '/') !== 0) {
            $pointer = '/' . $pointer;
        }

        return array_map(
            function ($segment) {
                return str_replace('~0', '~', str_replace('~1', '/', $segment));
            },
            array_map(
                'urldecode',
                array_slice(explode('/', $pointer), 1)
            )
        );
    }
}
