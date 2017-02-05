<?php

namespace League\JsonReference\Pointer;

class InvalidPointerException extends \InvalidArgumentException
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
     * @param string $character
     *
     * @return static
     */
    public static function invalidFirstCharacter($character)
    {
        return new static(sprintf('A pointer must start with "/", got "%s"', $character));
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
