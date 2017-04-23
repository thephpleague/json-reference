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
     *
     * @return static
     */
    public static function nonexistentValue($value)
    {
        return new static(sprintf('The pointer referenced a value that does not exist.  The value was: "%s"', $value));
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
