<?php

namespace League\JsonReference\Test;

use League\JsonReference\Dereferencer;
use League\JsonReference\Reference;
use League\JsonReference\ReferenceSerializers\SafeReferenceSerializer;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    function test_it_can_proxy_property_access()
    {
        Reference::setDereferencerInstance(new Dereferencer());
        $ref = new Reference(new SafeReferenceSerializer(), '#/obj', '', json_decode('{"obj": { "a": "1", "b": "2"} }'));
        $this->assertSame('1', $ref->a);
        $this->assertSame('2', $ref->b);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function test_it_throws_when_accessing_undefined_properties()
    {
        Reference::setDereferencerInstance(new Dereferencer());
        $ref = new Reference(new SafeReferenceSerializer(), '#/obj', '', json_decode('{"obj": { "a": "1", "b": "2"} }'));
        $ref->c;
    }

    function test_it_can_iterate_objects()
    {
        Reference::setDereferencerInstance(new Dereferencer());
        $ref = new Reference(new SafeReferenceSerializer(), '#/obj', '', json_decode('{"obj": { "a": "1", "b": "2"} }'));
        $vars = [];
        foreach ($ref as $k => $v) {
            $vars[$k] = $v;
        }
        $this->assertSame(['a' => '1', 'b' => '2'], $vars);
    }

    function test_it_can_iterate_arrays()
    {
        Reference::setDereferencerInstance(new Dereferencer());
        $ref = new Reference(new SafeReferenceSerializer(), '#/arr', '', json_decode('{"arr": [1,2,3] }'));
        $vars = [];
        foreach ($ref as $k => $v) {
            $vars[$k] = $v;
        }
        $this->assertSame([1,2,3], $vars);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function test_it_throws_when_iterating_non_iterable_types()
    {
        Reference::setDereferencerInstance(new Dereferencer());
        $ref = new Reference(new SafeReferenceSerializer(), '#/inv', '', json_decode('{"inv": 1 }'));
        foreach ($ref as $k => $v) {

        }
    }

    /**
     * @expectedException \RuntimeException
     */
    function test_it_throws_when_resolving_without_a_dereferencer()
    {
        Reference::setDereferencerInstance(null);
        $ref = new Reference(new SafeReferenceSerializer(), 'file://my-schema');
        $ref->resolve();
    }

    function test_it_json_serializes_using_the_reference_serializer()
    {
        $ref = new Reference(new SafeReferenceSerializer(), '#/obj');
        $this->assertSame(json_encode(['$ref' => '#/obj']), json_encode($ref));
    }
}
