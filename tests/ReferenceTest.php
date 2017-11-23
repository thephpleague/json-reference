<?php

namespace Activerules\JsonReference\Test;

use Activerules\JsonReference\Dereferencer;
use Activerules\JsonReference\Reference;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    function test_it_can_proxy_property_access()
    {
        $ref = new Reference('#/obj', '', json_decode('{"obj": { "a": "1", "b": "2"} }'));
        $this->assertSame('1', $ref->a);
        $this->assertSame('2', $ref->b);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function test_it_throws_when_accessing_undefined_properties()
    {
        $ref = new Reference('#/obj', '', json_decode('{"obj": { "a": "1", "b": "2"} }'));
        $ref->c;
    }

    function test_it_can_iterate_objects()
    {
        $ref = new Reference('#/obj', '', json_decode('{"obj": { "a": "1", "b": "2"} }'));
        $vars = [];
        foreach ($ref as $k => $v) {
            $vars[$k] = $v;
        }
        $this->assertSame(['a' => '1', 'b' => '2'], $vars);
    }

    function test_it_can_iterate_arrays()
    {
        $ref = new Reference('#/arr', '', json_decode('{"arr": [1,2,3] }'));
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
        $ref = new Reference('#/inv', '', json_decode('{"inv": 1 }'));
        foreach ($ref as $k => $v) {

        }
    }

    /**
     * @expectedException \RuntimeException
     */
    function test_it_throws_when_resolving_without_a_dereferencer()
    {
        $ref = new Reference('file://my-schema');
        $ref->resolve();
    }

    function test_it_json_serializes_using_the_reference_serializer()
    {
        (new Dereferencer());
        $ref = (new Reference('#/obj'));
        $this->assertSame(json_encode(['$ref' => '#/obj']), json_encode($ref));
    }
}
