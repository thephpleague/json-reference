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
     * @var string
     */
    private $pointer;

    /**
     * @param string $value The referenced value.
     * @param string $pointer The full pointer which contains the invalid value.
     */
    public function __construct($value, $pointer)
    {
        $this->value = $value;
        $this->pointer = $pointer;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getPointer()
    {
        return $this->pointer;
    }
}
