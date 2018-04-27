<?php

namespace League\JsonReference\Pointer;

final class InvalidPointerException extends \InvalidArgumentException
{
    /**
     * @param string $type
     *
     * @return static
     */
    public static function invalidType($type)
    {
        return new static(sprintf('Only strings are valid pointers, got "%s"', $type));
    }

    /**
     * @param string $value
     * @param string $pointer
     *
     * @return static
     */
    public static function nonexistentValue($value, $pointer)
    {
        return new static(sprintf(
            'The pointer "%1$s" referenced a value "%2$s" that does not exist.',
            $pointer,
            $value
        ));
    }

    /**
     * @param string $target
     *
     * @return static
     */
    public static function invalidTarget($target)
    {
        return new static(sprintf('Cannot set the value for %s because it is not within object or array', $target));
    }
}
