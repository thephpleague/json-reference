<?php

namespace League\JsonReference;

/**
 * A Reference object represents an internal $ref in a JSON object.
 * Because JSON references can be circular, in-lining the reference is
 * impossible.  This object can be substituted for the $ref instead,
 * allowing lazy resolution of the $ref when needed.
 */
final class Reference implements \JsonSerializable, \IteratorAggregate
{
    /**
     * @var \League\JsonReference\Dereferencer|null
     */
    private static $dereferencer;

    /**
     * @var string
     */
    private $ref;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var object|null
     */
    private $schema;

    /**
     * @var mixed
     */
    private $resolved;

    /**
     * @param string $ref
     * @param string $scope
     * @param null   $schema
     */
    public function __construct($ref, $scope = '', $schema = null)
    {
        $this->ref    = $ref;
        $this->scope  = $scope;
        $this->schema = $schema;
    }

    /**
     * Specify data which should be serialized to JSON.
     * Because a reference can be circular, references are always
     * re-serialized as the reference property instead of attempting
     * to inline the data.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return ['$ref' => $this->ref];
    }

    /**
     * Resolve the reference and return the data.
     *
     * @return mixed
     */
    public function resolve()
    {
        if (isset($this->resolved)) {
            return $this->resolved;
        }

        $pointer = new Pointer($this->schema);
        if (is_internal_ref($this->ref) && $pointer->has(ltrim($this->ref, '#'))) {
            return $this->resolved = $pointer->get(ltrim($this->ref, '#'));
        }

        return $this->dereferencer()->dereference(resolve_uri($this->ref, $this->scope));
    }

    /**
     * Proxies property access to the underlying schema.
     *
     * @param  string $property
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __get($property)
    {
        $schema = $this->resolve();
        if (isset($schema->$property)) {
            return $schema->$property;
        }

        throw new \InvalidArgumentException(sprintf('Unknown property "%s"', $property));
    }

    /**
     * Retrieve an external iterator
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        $schema = $this->resolve();

        if (!is_object($schema) && !is_array($schema)) {
            throw new \InvalidArgumentException(
                sprintf('Expected an object or array, got "%s"', gettype($schema))
            );
        }

        return new \ArrayIterator(is_object($schema) ? get_object_vars($schema) : $schema);
    }

    /**
     * @param \League\JsonReference\Dereferencer|null $dereferencer
     */
    public static function setDereferencerInstance(Dereferencer $dereferencer = null)
    {
        static::$dereferencer = $dereferencer;
    }

    /**
     * @return \League\JsonReference\Dereferencer
     *
     * @throws \RuntimeException
     */
    private function dereferencer()
    {
        if (!static::$dereferencer) {
            throw new \RuntimeException(
                sprintf('The reference %s cannot be resolved without a Dereferencer.', $this->ref)
            );
        }

        return static::$dereferencer;
    }
}
