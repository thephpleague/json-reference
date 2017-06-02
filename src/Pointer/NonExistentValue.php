<?php

namespace League\JsonReference\Pointer;

/**
 * Represents a value referenced by a pointer that does not exist in the JSON data.
 */
final class NonExistentValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value The referenced value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
